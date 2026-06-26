import { version, logger } from "./utils.mjs";

window.onload = async () => {
  logger.init();
  version.init();
  switch (version.console) {
    case 4:
      const ps4 = await import("./ps4/userland.mjs");
      await ps4.main();
      break;
    case 5:
      //TODO
      break;
    default:
      logger.info(`Unsupported console ${version.console}`);
  }
};
