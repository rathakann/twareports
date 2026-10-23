@props(['name', 'id' => null, 'value' => null, 'error' => null])
<x-form.input :name="$name" :id="$id" :value="$value" :error="$error" icon="calendar-days" class="js-datepicker" autocomplete="off" {{ $attributes }} />
