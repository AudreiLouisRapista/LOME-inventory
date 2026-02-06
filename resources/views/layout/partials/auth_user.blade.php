@if(session(key: 'id') == null)
    {{ unauthorize() }}
@endif