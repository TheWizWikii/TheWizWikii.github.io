/*                    ,-,-
                     / / |
   ,-'             _/ / /
  (-_          _,-' `Z_/
   "#:      ,-'_,-.    \  _
    #'    _(_-'_()\     \" |
  ,--_,--'                 |
 / ""                      L-'\
 \,--^---v--v-._        /   \ |
   \_________________,-'      |
                    \
                     \
                      \
 NOTE: The code in this file will be executed multiple times! */

let top_window = window;
let is_iframe  = false;
let top;

try {
  // Have to access top window's prop (document) to trigger same-origin DOMException
  // so we can catch it and act accordingly.
  top = window.top.document ? window.top : false;
} catch(e) {
  // Can't access top, it means we're inside a different domain iframe.
  top = false;
}

if (top && top.__Cypress__) {
  if (window.parent === top) {
    top_window = window;
    is_iframe  = false;

  } else {
    top_window = window.parent;
    is_iframe  = true;
  }

} else if (top) {
  top_window = top;
  is_iframe  = top !== window.self;
}

export {
  top_window,
  is_iframe,
};
