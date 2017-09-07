hello , {{ $name }} !
<ul>
    @foreach($list as $item)
        <li>姓名：{{ $item['name'] }} -- 年龄：{{ $item['age'] }}</li>
    @endforeach
</ul>