@foreach($endpoint->httpMethods as $method)
```javascript
axios({
    method: '{{ strtolower($method) }}',
    url: '{{ $baseUrl }}/{{ $endpoint->boundUri }}',
    @if(count($endpoint->headers))
    headers: {
        @foreach($endpoint->headers as $key => $value)
        '{{ $key }}': '{{ $value }}',
        @endforeach
    },
    @endif
    @if(count($endpoint->cleanQueryParameters))
    params: {
        @foreach($endpoint->cleanQueryParameters as $key => $param)
        '{{ $key }}': {!! json_encode($param['example'] ?? '') !!},
        @endforeach
    },
    @endif
    @if(count($endpoint->cleanBodyParameters))
    data: {
        @foreach($endpoint->cleanBodyParameters as $key => $param)
        '{{ $key }}': {!! json_encode($param['example'] ?? '') !!},
        @endforeach
    },
    @endif
})
.then(response => {
    console.log(response.data);
})
.catch(error => {
    console.error(error.response ? error.response.data : error.message);
});
```
@endforeach