@php
    $action = $filters['action'] ?? request()->url();
    $method = $filters['method'] ?? 'GET';
    $inputs = $filters['inputs'] ?? [];
@endphp

<form method="{{ $method }}" action="{{ $action }}" class="mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-white rounded-xl shadow">
        @foreach ($inputs as $name => $config)
            @php
                $parts = explode('|', $config);
                $type = 'text';
                $id = $name;
                $label = ucfirst($name);
                $placeholder = '';
                $options = [];

                foreach ($parts as $part) {
                    if (str_starts_with($part, 'type:')) {
                        $type = explode(':', $part)[1];
                    } elseif (str_starts_with($part, 'id:')) {
                        $id = explode(':', $part)[1];
                    } elseif (str_starts_with($part, 'label:')) {
                        $label = explode(':', $part)[1];
                    } elseif (str_starts_with($part, 'placeholder:')) {
                        $placeholder = explode(':', $part)[1];
                    } elseif (str_starts_with($part, 'options:')) {
                        $optString = explode(':', $part, 2)[1];
                        $options = json_decode($optString, true) ?? [];
                    }
                }

                $value = old($name, request($name));
            @endphp

            <div>
                <label for="{{ $id }}" class="block text-sm font-medium">{{ $label }}</label>

                @if ($type === 'select')
                    <select name="{{ $name }}" id="{{ $id }}" class="w-full rounded-lg border-gray-300">
                        @if ($placeholder)
                            <option value="">{{ $placeholder }}</option>
                        @endif
                        @foreach ($options as $key => $text)
                            <option value="{{ $key }}" @selected($value == (string) $key)>{{ $text }}</option>
                        @endforeach
                    </select>
                @else
                    <input
                        type="{{ $type }}"
                        name="{{ $name }}"
                        id="{{ $id }}"
                        value="{{ $value }}"
                        placeholder="{{ $placeholder }}"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                    >
                @endif
            </div>
        @endforeach

        <div class="col-span-1 md:col-span-4 flex justify-end gap-2 mt-2">
            <a href="{{ url()->current() }}" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-sm">
                Limpar
            </a>
            <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                Filtrar
            </button>
        </div>
    </div>
</form>
