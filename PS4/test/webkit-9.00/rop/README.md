This is a tool for writing ROP.

`usage: compiler.py source.rop gadgets.txt > output.js`

* `source.rop` is the actual code (see `test.rop`)
* `gadgets.txt` is a file of the following format:

`<module>-gadgets.txt:<hex_offset> : <instructions>`

The format after the first colon is the ROPgadget's output format.
