import os
import sys
import subprocess

dumpsPath=sys.argv[1]
if dumpsPath.endswith('/'):
	dumpsPath=dumpsPath[:-1]

procFiles=["libc-gadgets.txt","libc.elf","libkernel.elf","syscalls.txt"]
flData={}
for fls in procFiles:
	flpath="{}/{}".format(dumpsPath,fls)
	if not os.path.isfile(flpath):
		print ("please make sure you have these ( {} ) file in folder {}".format(", ".join(procFiles),dumpsPath))
		sys.exit()
	if fls.endswith("elf"):
		ProcOut=subprocess.check_output('objdump -d "{}"'.format(flpath), shell=True).decode()
		flData[fls]	=ProcOut.split("\n")
	else:
		flData[fls]	=open(flpath).read().strip().split("\n")

ropValue={"saveall_addr":"",
		  "loadall_addr":"",
		  "pivot_addr":"",
		  "infloop_addr":"",
		  "jop_frame_addr":"",
		  "get_errno_addr_addr":"",
		  "pthread_create_addr":""}

for j, ln in enumerate(flData["libc-gadgets.txt"]):
	if ln.count(": mov rsp, qword ptr [rdi + 0x38] ; pop rdi ; ret") and ropValue["pivot_addr"]=="":
		ropValue["pivot_addr"]=hex(int(ln.split(" ")[0],16))
	if ln.count(": push rbp ; mov rbp, rsp ; mov rax, qword ptr [rdi] ; call qword ptr [rax]") and ropValue["jop_frame_addr"]=="":
		ropValue["jop_frame_addr"]=hex(int(ln.split(" ")[0],16))
					
for k, ln in enumerate(flData["libc.elf"]):
	if ln.count("eb fe                	jmp") and ropValue["infloop_addr"]=="":
		ropValue["infloop_addr"]=hex(int("0x{}".format(ln.strip().split(":")[0]),16))
				
	if ln.count("mov    %rbx,0x8(%rdi)") and ropValue["saveall_addr"]=="":
		if flData["libc.elf"][k-1].count("mov    %rax,(%rdi)") :
			ropValue["saveall_addr"]=hex(int("0x{}".format(flData["libc.elf"][k-1].strip().split(":")[0]),16))

	if ln.count("sub    $0x10,%rax") and ropValue["loadall_addr"]=="":
		if flData["libc.elf"][k-1].count("mov    0x38(%rdi),%rax"):
			ropValue["loadall_addr"]=hex(int("0x{}".format(flData["libc.elf"][k-1].strip().split(":")[0]),16))
thrNewAdd=""
for ln in flData["syscalls.txt"]:
	if ln.count("sys_455_addr"):
		thrNewAdd=ln.split("+")[-1].replace(";","").strip()

for m, ln in enumerate(flData["libkernel.elf"]):
	if ln.count("callq  {}".format(thrNewAdd.replace("0x",""))) and ropValue["pthread_create_addr"]=="" and thrNewAdd!="":
		n=1
		while m-n>0:
			if flData["libkernel.elf"][m-n].count("push   %rbp"):
				ropValue["pthread_create_addr"]=hex(int("0x{}".format(flData["libkernel.elf"][m-n].strip().split(":")[0]),16))
				break
			n+=1
	if ln.count("syscall") and ropValue["get_errno_addr_addr"]==""  :
		p=1
		sub1=""
		sub2=""
		while p+1 < len(flData["libkernel.elf"]):
			if flData["libkernel.elf"][m+p].count("jb"):
				sub1=flData["libkernel.elf"][m+p].split("     ")[-1].split(" ")[0]
				for subln in (flData["libkernel.elf"]):
					if subln.count("{}:".format(sub1)):
						sub2=subln.split("#")[-1].strip().split(" ")[0].strip()
						break
				if sub2 !="":
					for q, subln in enumerate(flData["libkernel.elf"]):
						if subln.count("{}:".format(sub2)):
							ropValue["get_errno_addr_addr"]=hex(int("0x{}".format(flData["libkernel.elf"][q+1].split("  ")[-1].split(" ")[0]),16))
				break
			p+=1
			
ropText="""var saveall_addr = libc_base+<saveall_addr>;
var loadall_addr = libc_base+<loadall_addr>;
var pivot_addr = libc_base+<pivot_addr>;
var infloop_addr = libc_base+<infloop_addr>;
var jop_frame_addr = libc_base+<jop_frame_addr>;
var get_errno_addr_addr = libkernel_base+<get_errno_addr_addr>;
var pthread_create_addr = libkernel_base+<pthread_create_addr>;"""
for ks in ropValue.keys():
	if ropValue[ks]=="":
		print ("unable to find all the values automatiaccly, Please try manual method else some thing wrong with dump")
		sys.exit()
	ropText=ropText.replace("<{}>".format(ks),ropValue[ks])
		
print(ropText)
			
