export const logger = {
  verbose: true, // enable for debug logs
  init() {
    this.console = document.getElementById("console");
  },
  info(msg) {
    this.log(`[+] ${msg}`);
  },
  error(msg) {
    this.log(`[-] ${msg}`);
  },
  debug(msg) {
    if (this.verbose) {
      this.log(`[*] ${msg}`);
    }
  },
  log(msg) {
    this.console.append(`${msg}\n`);
  },
};
export const version = {
  console: undefined,
  major: undefined,
  minor: undefined,
  init() {
    const ua = navigator.userAgent;

    logger.info(`Agent: ${ua}`);

    const matches = ua.match(/PlayStation\s+(\d+)\/(\d+)\.(\d+)/);
    if (matches === null) {
      throw new Error(`${ua} not supported !!`);
    }

    this.console = parseInt(matches[1], 10);
    this.major = parseInt(matches[2], 10);
    this.minor = parseInt(matches[3], 16);
  },
  toString() {
    return `${this.major}.${this.minor.toString(16).padStart(2, "0")}`;
  },
};
