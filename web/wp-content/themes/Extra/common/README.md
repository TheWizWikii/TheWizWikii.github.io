# Common

- Anything here should be pure and not rely on anything from VB, TB, Core, so it can be reused wherever, even outside Divi.
- Only external dependencies allowed.

# Storybook

1. Go into common submodule directory
2. Switch to node version 20 e.g `nvm use 20`
3. Run `corepack enable` to activate [Corepack](https://nodejs.org/api/corepack.html)
4. Run `yarn set version berry`
5. Run `yarn install`
6. Run `yarn storybook`