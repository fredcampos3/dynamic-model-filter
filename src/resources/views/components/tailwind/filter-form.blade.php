<form method="GET" id="dynamic-filter-form">
    <div class="flex flex-col gap-4">
        <div class="rounded-xl shadow bg-white border border-gray-200">
            <div class="flex items-center justify-between px-4 py-2 border-b">
                <h3 class="font-semibold text-gray-700">FILTRO</h3>
                <button type="button" class="text-gray-500 hover:text-gray-700" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
            <div class="px-4 py-3">
                <div class="grid grid-cols-12 gap-4">
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
                            $label = $config['label'] ?? ucfirst(trans("reports.{$name}"));
                            $value = request($name) ?? ($config['default'] ?? null);
                        @endphp

                        <div class="col-span-{{ $size }} flex flex-col">
                            <label for="{{ $name }}" class="mb-1 font-medium text-sm text-gray-700">{{ $label }}</label>
                            @if ($type === 'select')
                                <select class="border rounded-md px-3 py-2 {{ $addClass }}" name="{{ $nameInput }}" id="{{ $name }}" {{ $attributes }}>
                                    @if (!isset($config['default']))
                                        <option value="">Selecione...</option>
                                    @endif
                                    @foreach ($data as $optionValue => $optionLabel)
                                        <option value="{{ $optionValue }}" @if($multi && is_array($value)) @selected(in_array((string) $optionValue, $value)) @else @selected((string) $value === (string) $optionValue) @endif>{{ $optionLabel }}</option>
                                    @endforeach
                                </select>
                            @elseif ($type === 'date')
                                <input type="date" class="border rounded-md px-3 py-2 {{ $addClass }}" name="{{ $nameInput }}" value="{{ $value }}" id="{{ $name }}" placeholder="{{ $placeholder }}" {{ $attributes }}>
                            @else
                                <input type="text" class="border rounded-md px-3 py-2 {{ $addClass }}" name="{{ $nameInput }}" value="{{ $value }}" id="{{ $name }}" placeholder="{{ $placeholder }}" {{ $attributes }}>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-end gap-2 bg-blue-50 px-4 py-3 rounded-b-xl">
                <a href="{{ url()->current() }}" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded">LIMPAR</a>
                <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white hover:bg-blue-700 rounded">APLICAR</button>
            </div>
        </div>
    </div>
</form>
