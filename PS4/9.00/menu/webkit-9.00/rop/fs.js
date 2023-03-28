var path_buf = malloc(0x1000);
var getdents_buf = malloc(0x1000);

function sys_open(path)
{
    var q = []
    for(var i = 0; i < path.length; i++)
        q.push(path.charCodeAt(i));
    q.push(0);
    write_mem(path_buf, q);
    var q = fcall(sys_5_addr, path_buf, 0);
    if(''+q == '255,255,255,255,255,255,255,255')
        return -1;
    var ans = 0;
    for(var i = 7; i >= 0; i--)
        ans = ans * 256 + q[i];
    return ans;
}

function sys_close(fd)
{
    fcall(sys_6_addr, fd);
}

function sys_getdents(fd)
{
    var q = fcall(sys_272_addr, fd, getdents_buf, 0x1000);
    if(''+q == '255,255,255,255,255,255,255,255')
        return null;
    var l = 0;
    for(var i = 7; i >= 0; i--)
        l = l * 256 + q[i];
    var ans = [];
    var offset = 0;
    while(offset < l)
    {
        var ll = read_mem(getdents_buf+offset+4, 2);
        var next = offset + ll[0] + 256*ll[1];
        var ll = read_mem(getdents_buf+offset+7, 1)[0];
        var name = read_mem(getdents_buf+offset+8, ll);
        offset = next;
        var s = '';
        for(var i = 0; i < ll; i++)
            s += String.fromCharCode(name[i]);
        ans.push(s);
    }
    return ans;
}

function listdir(path)
{
    var fd = sys_open(path);
    if(fd < 0)
        throw "open failed";
    var ans = [];
    while(true)
    {
        var q = sys_getdents(fd);
        if(!q)
        {
            sys_close(fd);
            throw "getdents failed";
        }
        if(!q.length)
            break;
        for(var i = 0; i < q.length; i++)
            ans.push(q[i]);
    }
    sys_close(fd);
    return ans;
}
