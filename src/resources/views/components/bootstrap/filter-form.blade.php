<form method="GET" id="dynamic-filter-form">
    <div class="row g-3">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">FILTRO</h5>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filter-body" aria-expanded="true"><i class="fa fa-minus"></i></button>
                </div>
                <div class="collapse show" id="filter-body">
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach ($fields as $name => $config)
                                @php
                                    $type = $config['type'] ?? 'text';
                                    $addClass = $config['class'] ?? '';
                                    $attributes = $config['attributes'] ?? '';
                                    $multi = str_contains($attributes, 'multiple');
                                    $nameInput = $multi ? $name."[]" : $name;
                                    $placeholder = $config['placeholder'] ?? '';
                                    $size = $config['size'] ?? 4;
                                    $data = $config['data'] ?? [];
                                    $label = $config['label'] ?? ucfirst($name);
                                    $value = request($name) ?? ($config['default'] ?? null);
                                @endphp

                                <div class="col-md-{{ $size }}">
                                    <div class="mb-3">
                                        <label for="{{ $name }}" class="form-label">{{ $label }}</label>
                                        @if ($type === 'select')
                                            <select class="form-select {{ $addClass }}" name="{{ $nameInput }}" id="{{ $name }}" {{ $attributes }}>
                                                @if (!isset($config['default']))
                                                    <option value="">Selecione...</option>
                                                @endif
                                                @foreach ($data as $optionValue => $optionLabel)
                                                    <option value="{{ $optionValue }}"@if($multi && is_array($value)) @selected(in_array((string) $optionValue, $value)) @else @selected((string) $value === (string) $optionValue)@endif>{{ $optionLabel }}</option>
                                                @endforeach
                                            </select>
                                        @elseif ($type === 'date')
                                            <input type="date" class="form-control {{ $addClass }}" name="{{ $nameInput }}" value="{{ $value }}" id="{{ $name }}" placeholder="{{ $placeholder }}" {{ $attributes }}>
                                        @else
                                            <input type="{{ $type }}" class="form-control {{ $addClass }}" name="{{ $nameInput }}" value="{{ $value }}" id="{{ $name }}" placeholder="{{ $placeholder }}" {{ $attributes }}>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer bg-light d-flex justify-content-end">
                        <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm me-2">LIMPAR</a>
                        <button type="submit" class="btn btn-primary btn-sm">APLICAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
