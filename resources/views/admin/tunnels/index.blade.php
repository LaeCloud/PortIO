<x-app-layout>
    <h1>隧道</h1>

    <form name="filter">
        Host ID: <input type="text" name="host_id" value="{{ Request::get('host_id') }}" />
        名称: <input type="text" name="name" value="{{ Request::get('name') }}" />

        <select name="protocol">
            <option value="">协议</option>
            <option value="http" @if (Request::get('protocol') == 'http') selected @endif>HTTP</option>
            <option value="https" @if (Request::get('protocol') == 'https') selected @endif>HTTPS</option>
            <option value="tcp" @if (Request::get('protocol') == 'tcp') selected @endif>TCP</option>
            <option value="udp" @if (Request::get('protocol') == 'udp') selected @endif>UDP</option>
            <option value="xtcp" @if (Request::get('protocol') == 'xtcp') selected @endif>XTCP</option>
            <option value="stcp" @if (Request::get('protocol') == 'stcp') selected @endif>STCP</option>
            <option value="sudp" @if (Request::get('protocol') == 'sudp') selected @endif>SUDP</option>
        </select>



        <button type="submit">筛选</button>
    </form>

    <p>总计: {{ $count }}</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>名称</th>
                <th>客户</th>
                <th>状态</th>
                <th scope="col">协议</th>
                <th scope="col">本地地址</th>
                <th scope="col">远程端口/域名</th>
                <th scope="col">连接数</th>
                <th scope="col">下载流量</th>
                <th scope="col">上载流量</th>
                <th scope="col">服务器</th>
                <th scope="col">隧道状态</th>
                <th>创建时间</th>
                <th>更新时间</th>
                <th>操作</th>
            </tr>
        </thead>


        <tbody>
            @foreach ($hosts as $host)
                <tr>
                    <td>{{ $host->host_id }}</td>
                    <td>{{ $host->name }}</td>
                    <td>{{ $host->user->name }}</td>
                    <td>{{ $host->status }}</td>
                    @php($cache = Cache::get('frpTunnel_data_' . $host->client_token, ['status' => 'offline']))
                    <td>{{ strtoupper($host->protocol) }}</td>
                    <td>{{ $host->local_address }}</td>

                    @if ($host->protocol == 'http' || $host->protocol == 'https')
                        <td>{{ $host->custom_domain }}</td>
                    @else
                        <td>{{ $host->server->server_address . ':' . $host->remote_port }}</td>
                    @endif

                    <td>{{ $cache['cur_conns'] ?? 0 }}</td>
                    <td>{{ unitConversion($cache['today_traffic_in'] ?? 0) }}</td>
                    <td>{{ unitConversion($cache['today_traffic_out'] ?? 0) }}</td>

                    <td><a href="{{ route('admin.servers.show', $host->server->id) }}">{{ $host->server->name }}</a></td>

                    <td>
                        @if($host->locked_reason)
                            <span class="text-danger">被锁定，因为 {{ $host->locked_reason }}</span>
                            <br />
                        @endif
                        @if ($cache['status'] === 'online')
                            <span class="text-success">在线</span>
                        @else
                            <span class="text-danger">离线</span>
                        @endif
                    </td>

                    <td>{{ $host->created_at }}</td>
                    <td>{{ $host->updated_at }}</td>
                    <td>
                        <a href="{{ route('admin.tunnels.show', ['tunnel' => $host]) }}">编辑</a>

                        <form action="{{ route('admin.tunnels.destroy', ['tunnel' => $host]) }}" method="POST"
                            onsubmit="return confirm('真的要删除吗？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">删除</button>
                        </form>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>


    {{ $hosts->links() }}
</x-app-layout>
