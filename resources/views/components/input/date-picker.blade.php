<div>
    <input
        class=" w-full px-4 py-2 appearance-none bg-gray-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md active:border-slate-500 "
     readonly
        x-data
        x-ref="input"
        x-init="new Pikaday({
            field: $refs.input,
            format:'d/m/YYYY',
            onSelect: function() {
                console.log(this.toString());
                $('#'+$el.getAttribute('hidden_element')).val( this.toString() );
    }
        })"
        type="text"
        {{ $attributes }}
    >
</div>
