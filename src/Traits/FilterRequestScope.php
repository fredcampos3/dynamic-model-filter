<?php

namespace TecnoCampos\DynamicModelFilter\Traits;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

trait FilterRequestScope
{
    public function scopeApplyFilters(Builder $query): Builder
    {
        $requestData = request()->all();
        $filters = $this->getFilterRequest($query, $requestData);
        foreach ($filters as $key => $value) {
            if ((isset($value) && $value !== '') && str_contains($key, '#')) {
                [$field, $lines] = explode('#', $key);
                [$type, $options] = array_pad(explode('|', $lines, 2), 2, null);
                $value = $value == 'none' ? null : $value;

                switch ($type) {
                    case 'text':
                        $query->where($field, $value);
                        break;

                    case 'date':
                        $date = $this->convertDate($value);
                        $query->whereDate($field, $date);
                        break;

                    case 'between':
                        [$position, $column, $source] = array_pad(explode('|', $options), 3, null);
                        $operator = $position == 'first' ? '>=' : '<=';
                        $column = explode(':', $column)[1];

                        if ($source != null && str_contains($source, 'request')) {
                            [$request, $typeData] = explode(':', $source);
                            $column = $requestData[$request]??null;
                            if (empty($column)) {
                                break;
                            }

                            if (str_contains($column, ':')) {
                                [$valueColumn, $relations] = explode(':', $column, 2);
                                if (str_contains($relations, '>')) {
                                    [$beforeArrow, $afterArrow] = explode('>', $relations);
                                    $relations = $beforeArrow;
                                    $typeSearch = $afterArrow;
                                }else{
                                    $typeSearch = 'between';
                                    $valueColumn = "$position-$valueColumn";
                                }
                                $query = $this->relationQuery($query, $relations, $valueColumn, $value, $typeSearch);
                                break;
                            }
                        }

                        $date = $this->convertDate($value);
                        $query->whereDate($column, $operator, $date);
                        break;

                    case 'relation':
                        [$typeSearch, $column, $relations] = explode('|', $options);

                        $query = $this->relationQuery($query, $relations, $column, $value, $typeSearch);
                        break;

                    case 'like':
                        $query->where($field, 'like', "%$value%");
                        break;

                    case 'multi':
                        [$typeSearch, $columns] = explode('|', $options);
                        $query = $this->multiQuery($query, $typeSearch, $columns, $value);
                        break;
                }
            }
        }

        return $query;
    }

    public function relationQuery($query, $relation, $column, $value, $type)
    {
        $column = explode(':', $column)[1];
        $type = explode(':', $type)[1];

        switch ($type) {
            case 'text':
                $query->whereHas($relation, fn($q) => $q->where($column, $value));
                break;

            case 'date':
                $date = $this->convertDate($value);
                $query->whereHas($relation, fn($q) => $q->whereDate($column, $date));
                break;

            case 'between':
                [$position, $column] = explode('-', $column);

                $operator = $position == 'first' ? '>=' : '<=';
                $date = $this->convertDate($value);
                $query->whereHas($relation, fn($q) => $q->whereDate($column, $operator, $date));
                break;

            case 'like':
                $query->whereHas($relation, fn($q) => $q->where($column, 'like', "%$value%"));
                break;
        }

        return $query;
    }

    public function multiQuery($query, $type, $columns, $value)
    {
        $arrayColumns = explode(',', explode(':', $columns)[1]);
        $type = explode(':', $type)[1];

        switch ($type) {
            case 'text':
                $query->where(function ($q) use ($arrayColumns, $value) {
                    foreach ($arrayColumns as $column) {
                        $q->orWhere($column, $value);
                    }
                });
                break;

            case 'date':
                $date = $this->convertDate($value);
                $query->where(function ($q) use ($arrayColumns, $date) {
                    foreach ($arrayColumns as $column) {
                        $q->orWhereDate($column, $date);
                    }
                });
                break;

            case 'like':
                $query->where(function ($q) use ($arrayColumns, $value) {
                    foreach ($arrayColumns as $column) {
                        $q->orWhere($column, 'like', "%$value%");
                    }
                });
                break;
        }

        return $query;
    }

    public function convertDate($date)
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        $locale = app()->getLocale();
        $format = $locale === 'pt_BR' ? 'd/m/Y' : 'm/d/Y';

        try {
            return Carbon::createFromFormat($format, $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFilterRequest($query, array $requestData)
    {
        $filterMap = $query->getModel()::$filterRequest ?? [];

        return collect($requestData)->filter(fn($v, $k) => isset($filterMap[$k]) && $v !== '')
        ->mapWithKeys(function ($v, $k) use ($filterMap) {
            $map = $filterMap[$k];
            $key = str_contains($map, '#') ? $map : "$k#$map";
            return [$key => $v];
        })->toArray();
    }
}

