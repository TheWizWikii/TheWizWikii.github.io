#python3
#program to search GOT start from webkit elf
#requirements objdump


import os
import sys
import subprocess

cmd="""objdump -D "webkit.elf" | grep 'call[^%]*$' | cut -d "$(printf '\t')" -f 3- | sort | uniq | less"""
cmd=cmd.replace("webkit.elf",sys.argv[1])
ProcOut=subprocess.check_output(cmd, shell=True).decode()
ProcList=ProcOut.split("\n")
ProcList.sort(reverse=True)

del ProcOut
lastKnown=0
for k,ln in enumerate(ProcList):
	if not ln.startswith("callq  fff"):
		continue;
	address=ln.split(" ")[2]
	number16=(1 << 64) - int("0x{}".format(address),16)
	if number16-lastKnown>500000:
		j=0
		while True:
			number161=(1 << 64) - int("0x{}".format(ProcList[k-j].split(" ")[2]),16)
			number162=(1 << 64) - int("0x{}".format(ProcList[k-j-1].split(" ")[2]),16)

			if(number161-number162)<100:
				print ("Address match at: {}".format(ProcList[k-j].split(" ")[2]))
				print("you can start searching for GOT at: {}".format(number161))
				sys.exit()				
			j=j+1


		break;
	lastKnown=number16
