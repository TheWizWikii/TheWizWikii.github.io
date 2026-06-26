import { version } from "../utils.mjs";

export class Offsets {
  static get current() {
    if (Offsets._current === undefined) {
      let matched = false;
      for (const type of Offsets.types) {
        if (type.version === version.toString()) {
          matched = true;
          Offsets._current = new type();
          break;
        }
      }

      if (!matched) {
        throw new Error(`Unable to find impl for ${version} !!`);
      }
    }

    return Offsets._current;
  }
}

class V900 extends Offsets {
  static get version() { return "9.00"; }
  get wk_CSSFontFace_sizeof() { return 0xb8; }
  get wk_CSSFontFace_m_families() { return 0x10; }
  get wk_CSSFontFace_m_featureSettings_m_buffer() { return 0x28; }
  get wk_CSSFontFace_m_featureSettings_m_size() { return 0x30; }
  get wk_CSSFontFace_m_featureSettings_m_capacity() { return 0x34; }
  get wk_CSSFontFace_m_clients() { return 0x60; }
  get wk_CSSFontFace_m_wrapper() { return 0x68; }
  get wk_CSSFontFace_m_status() { return 0x82; }
  get wk_CSSFontFace_m_thread() { return 0xa8; }
  get wk_RET() { return 0x2bf0769n; }
  get wk_LEAVE_RET() { return 0x220a168n; }
  get wk_POP_R8_RET() { return 0x1e262f8n; }
  get wk_POP_R9_RET() { return 0x12a7b96n; }
  get wk_POP_R10_RET() { return 0x125abffn; }
  get wk_POP_R11_RET() { return 0x1c33581n; }
  get wk_POP_R12_RET() { return 0x17c39e1n; }
  get wk_POP_R13_RET() { return 0x202adebn; }
  get wk_POP_R14_RET() { return 0x2105ec1n; }
  get wk_POP_R15_RET() { return 0x1c24b31n; }
  get wk_POP_RAX_RET() { return 0x1e67954n; }
  get wk_POP_RBP_RET() { return 0x685e6en; }
  get wk_POP_RBX_RET() { return 0x4d6758n; }
  get wk_POP_RCX_RET() { return 0x2c09fcdn; }
  get wk_POP_RDI_RET() { return 0x13c9c15n; }
  get wk_POP_RDX_RET() { return 0x155683bn; }
  get wk_POP_RSI_RET() { return 0x2bf0851n; }
  get wk_POP_RSP_RET() { return 0x685d81n; }
  get wk_MOV_RAX_RCX_RET() { return 0x2008fa0n; }
  get wk_MOV_QWORD_PTR_RDI_RAX_RET() { return 0x1eb1f1bn; }
  get wk_MOV_RAX_QWORD_PTR_RDI_RET() { return 0x16ba6f0n; }
  get wk_PUSH_RAX_POP_RBP_RET() { return 0x16d5cccn; }
  get wk_PUSH_RAX_PUSH_RBP_RET() { return 0x29ced40n; }
  get wk_PUSH_RBP_POP_RAX_RET() { return 0xb3b5d5n; } // push rbp; rol ch, 0xfb; pop rax; ret;
  get wk_POP_RAX_MOV_RAX_QWORD_PTR_RDI_JMP_QWORD_PTR_RAX_8() { return 0x143a842n; }
  get wk_PUSH_RBP_MOV_RBP_RSP_MOV_RAX_QWORD_PTR_RDI_CALL_QWORD_PTR_RAX_20() { return 0x141d420n; }
  get wk_MOV_RSI_QWORD_PTR_RAX_10_CALL_QWORD_PTR_RAX_18() { return 0x1f0d7e0n; }
  get wk_PUSH_RSI_JMP_QWORD_PTR_RAX() { return 0x294c0e2n; }
  get wk_MOV_RDI_RSI_30_MOV_RAX_QWORD_PTR_RDI_CALL_QWORD_PTR_RAX_38() { return 0xf33be4n; }
  get wk_expm1_builtin() { return 0x1d23560n; }
  get wk___imp___error() { return 0x2f4a4d0; }
  get wk___imp_strerror() { return 0x2f4a520; }
  get k__error() { return 0xcb80n; }
  get c_strerror() { return 0x394f0n; }
}

Offsets._current = Offsets._current || undefined;
Offsets.types = [V900];
