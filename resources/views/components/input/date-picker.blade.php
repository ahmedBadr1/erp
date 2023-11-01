<div>
    <input
        class=" w-full px-4 py-2 appearance-none bg-gray-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md active:border-slate-500 "
        x-data
        x-ref="input"
        x-init="new Pikaday({
            field: $refs.input,
            format:'D/M/YYYY',
            onSelect: function() {
{{--                console.log(this);--}}
                $('#'+$el.getAttribute('hidden_element')).val( dateToMySqlFormat(this._d) );
            }
        })"
        type="text"
        {{ $attributes }}
    >
</div>
