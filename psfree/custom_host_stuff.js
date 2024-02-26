async function runJailbreak() {
    window.jb_in_progress = true;
    window.jb_started = true;
    let l2_redirector = document.getElementById("l2-redirect");
    l2_redirector.style.opacity = "0";

    let postjb = document.getElementById("post-jb-view");
    postjb.style.opacity = "0";
    postjb.style.pointerEvents = "none";

    document.getElementById("run-jb-parent").style.opacity = "0";
    await sleep(500);
    document.getElementById("run-jb-parent").style.display = "none";
    document.getElementById("jb-progress").style.opacity = "1";
    await sleep(500);

    create_payload_buttons();

    document.getElementById("payloads-list").style.display = "none";
    document.getElementById("post-jb-view").style.display = "none";

    document.getElementById("payloads-list").style.opacity = "0";
    document.getElementById("post-jb-view").style.opacity = "0";

    setTimeout(async () => {
        let wk_exploit_type = localStorage.getItem("wk_exploit_type");
        if (wk_exploit_type == "psfree") {
            await run_psfree();
        } else if (wk_exploit_type == "fontface") {
            await run_fontface();
        }
    }, 100);
}

function wk_expoit_type_changed(event) {
    localStorage.setItem("wk_exploit_type", event.target.value);
}

function onload_setup() {

    if (document.documentElement.hasAttribute("manifest")) {
        add_cache_event_toasts();
    }

    create_redirector_buttons();

    document.documentElement.style.overflowX = 'hidden';
    let redirector = document.getElementById("redirector-view");
    let center_view = document.getElementById("center-view");

    let menu_overlay = document.getElementById("menu-overlay");
    let menu = document.getElementById("menu-bar-wrapper");

    if (localStorage.getItem("wk_exploit_type") == null) {
        localStorage.setItem("wk_exploit_type", "psfree");
    }

    let wk_exploit_type = localStorage.getItem("wk_exploit_type");
    if (wk_exploit_type == "psfree") {
        document.getElementById("wk-exploit-psfree").checked = true;
    } else if (wk_exploit_type == "fontface") {
        document.getElementById("wk-exploit-fontface").checked = true;
    }

    let isTransitionInProgress = false;

    center_view.style.transition = "left 0.4s ease, opacity 0.25s ease";
    center_view.style.pointerEvents = "auto";
    center_view.style.opacity = "1";
    redirector.style.pointerEvents = "none";
    redirector.style.opacity = "0";

    window.addEventListener('keydown', function (event) {
        if (event.keyCode == 51 || event.keyCode == 118) {
            // seems like the browser failes to load any new pages after the jailbreak...
            if (isTransitionInProgress || window.jb_in_progress || window.jb_started) {
                return;
            }
            isTransitionInProgress = true;
            if (redirector.style.left == "-100%") {
                redirector.style.left = "-30%";
                setTimeout(() => {
                    redirector.style.transition = "left 0.4s ease, opacity 0.25s ease";

                    center_view.style.pointerEvents = "none";
                    center_view.style.opacity = "0";
                    redirector.style.pointerEvents = "auto";
                    redirector.style.opacity = "1";

                    redirector.style.left = "0";
                    center_view.style.left = "30%";
                    setTimeout(() => {
                        center_view.style.transition = "none";
                        center_view.style.left = "100%";
                        isTransitionInProgress = false;
                    }, 420);
                }, 10);

            } else {
                center_view.style.left = "30%";

                setTimeout(() => {
                    center_view.style.transition = "left 0.4s ease, opacity 0.25s ease";

                    center_view.style.pointerEvents = "auto";
                    center_view.style.opacity = "1";
                    redirector.style.pointerEvents = "none";
                    redirector.style.opacity = "0";

                    redirector.style.left = "-30%";
                    center_view.style.left = "0";
                    setTimeout(() => {
                        redirector.style.transition = "none";
                        redirector.style.left = "-100%";
                        isTransitionInProgress = false;
                    }, 420);
                }, 10);


            }

        }


        if (event.keyCode == 52 || event.keyCode == 119) {
            if (isTransitionInProgress || window.jb_in_progress || window.jb_started) {
                return;
            }
            isTransitionInProgress = true;
            if (menu_overlay.style.top == "-100%") {
                menu_overlay.style.top = "0";
                menu_overlay.style.opacity = "1";
                menu.style.right = "0";
                setTimeout(() => {
                    isTransitionInProgress = false;
                }, 420);
            } else {
                menu_overlay.style.opacity = "0";
                menu.style.right = "-400px";
                setTimeout(() => {
                    menu_overlay.style.top = "-100%";
                    isTransitionInProgress = false;
                }, 420);

            }

        }
    });

    create_redirector_buttons();
}

function redirectorGo() {
    let redirector_input = document.getElementById("redirector-input");
    let redirector_input_value = redirector_input.value;
    if (redirector_input_value == "" || redirector_input_value == "http://") {
        showToast("Enter a valid URL.");
        return;
    }

    let redirector_history_store_raw = localStorage.getItem("redirector_history");

    if (redirector_history_store_raw == null) {
        localStorage.setItem("redirector_history", JSON.stringify([redirector_input_value]));
    }
    else {
        let redirector_history_store = JSON.parse(redirector_history_store_raw);

        redirector_history_store.unshift(redirector_input_value);

        localStorage.setItem("redirector_history", JSON.stringify(redirector_history_store));
    }


    window.location = redirector_input_value;
}

const default_pinned_websites = [
    "https://es7in1.site/ps5",
    "https://google.com"
]

const dummy_history = [
    "https://es7in1.site/ps5",
    "https://google.com",
    "https://ps5jb.pages.dev",
    "https://github.com",
    "https://duckduckgo.com",
    "https://youtube.com",
    "https://twitter.com",
    "https://reddit.com",
    "https://facebook.com",
    "https://instagram.com",
    "https://amazon.com",
    "https://wikipedia.org",
    "https://netflix.com"
]

function create_redirector_buttons() {
    let redirector_pinned_store_raw = localStorage.getItem("redirector_pinned");

    if (redirector_pinned_store_raw == null) { // || redirector_pinned_store_raw == "[]"
        localStorage.setItem("redirector_pinned", JSON.stringify(default_pinned_websites));
        redirector_pinned_store_raw = localStorage.getItem("redirector_pinned");
    }

    let redirector_pinned_store = JSON.parse(redirector_pinned_store_raw);

    const redirector_pinned = document.getElementById("redirector-pinned");

    redirector_pinned.innerHTML = "";

    let pinned_text = document.createElement("p");
    pinned_text.innerHTML = "Pinned";
    pinned_text.style.textAlign = "center";

    redirector_pinned.appendChild(pinned_text);


    for (let i = 0; i < redirector_pinned_store.length; i++) {
        let div = document.createElement("div");
        div.style.display = "flex";

        let a1 = document.createElement("a");
        a1.className = "btn small-btn";
        a1.tabIndex = "0";
        a1.innerHTML = redirector_pinned_store[i];
        a1.onclick = () => {
            window.location = redirector_pinned_store[i];
        };

        div.appendChild(a1);

        let a2 = document.createElement("a");
        a2.className = "btn icon-btn";
        a2.tabIndex = "0";
        a2.innerHTML = '<svg width="24px" height="24px" fill="#ddd"><use href="#delete-icon" /></svg>';
        a2.onclick = () => {
            let pinned_raw = localStorage.getItem("redirector_pinned");
            let pinned = JSON.parse(pinned_raw);
            // pinned = pinned.filter(item => item !== redirector_pinned_store[i]);
            pinned.splice(i, 1);
            localStorage.setItem("redirector_pinned", JSON.stringify(pinned));
            create_redirector_buttons();
        };

        div.appendChild(a2);


        redirector_pinned.appendChild(div);
    }

    let redirector_history_store_raw = localStorage.getItem("redirector_history");

    if (redirector_history_store_raw == null) {
        localStorage.setItem("redirector_history", JSON.stringify([]));
        redirector_history_store_raw = localStorage.getItem("redirector_history");
    }


    let redirector_history_store = JSON.parse(redirector_history_store_raw);

    // history stuff
    let redirector_history = document.getElementById("redirector-history");

    redirector_history.innerHTML = "";

    let history_text = document.createElement("p");
    history_text.innerHTML = "History";
    history_text.style.textAlign = "center";

    redirector_history.appendChild(history_text);


    for (let i = 0; i < redirector_history_store.length; i++) {
        let div = document.createElement("div");
        div.style.display = "flex";

        let a1 = document.createElement("a");
        a1.className = "btn small-btn";
        a1.tabIndex = "0";
        a1.innerHTML = redirector_history_store[i];
        a1.onclick = () => {
            window.location = redirector_history_store[i];
        };
        div.appendChild(a1);

        let a2 = document.createElement("a");
        a2.className = "btn icon-btn";
        a2.tabIndex = "0";
        a2.innerHTML = "&#9733;"
        a2.onclick = () => {
            let pinned_raw = localStorage.getItem("redirector_pinned");
            let pinned = JSON.parse(pinned_raw);
            pinned.unshift(redirector_history_store[i]);
            localStorage.setItem("redirector_pinned", JSON.stringify(pinned));
            create_redirector_buttons();
        };
        div.appendChild(a2);

        let a3 = document.createElement("a");
        a3.className = "btn icon-btn";
        a3.tabIndex = "0";
        a3.innerHTML = '<svg width="24px" height="24px" fill="#ddd"><use href="#delete-icon" /></svg>';
        a3.onclick = () => {
            let history_raw = localStorage.getItem("redirector_history");
            let history = JSON.parse(history_raw);
            // history = history.filter(item => item !== redirector_history_store[i]);
            history.splice(i, 1);
            localStorage.setItem("redirector_history", JSON.stringify(history));
            create_redirector_buttons();
        };
        div.appendChild(a3);

        redirector_history.appendChild(div);
    }




}

async function switch_to_post_jb_view() {
    // should already be none but just in case
    document.getElementById("run-jb-parent").style.display = "none";

    document.getElementById("jb-progress").style.opacity = "0";
    await sleep(1000);
    document.getElementById("jb-progress").style.display = "none";

    document.getElementById("post-jb-view").style.opacity = "0";
    document.getElementById("post-jb-view").classList.add("opacity-transition");
    document.getElementById("post-jb-view").style.display = "flex";
    document.getElementById("post-jb-view").style.opacity = "1";

    document.getElementById("credits").style.opacity = "0";
    document.getElementById("credits").style.display = "none";

}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function create_payload_buttons() {
    window.local_payload_queue = [];
    for (let i = 0; i < payload_map.length; i++) {
        let btn = document.createElement("a");
        btn.id = "payload-" + i;
        btn.className = "btn mx-auto";
        btn.tabIndex = "0";
        btn.opacity = 0;
        btn.onclick = async () => {
            showToast(payload_map[i].displayTitle + " added to queue.", 1000);
            window.local_payload_queue.push(payload_map[i]);
        };

        let btn_child = document.createElement("p");
        btn_child.className = "payload-name";
        btn_child.innerHTML = payload_map[i].displayTitle;
        btn.appendChild(btn_child);

        let btn_child2 = document.createElement("p");
        btn_child2.className = "payload-description";
        btn_child2.innerHTML = payload_map[i].description;
        btn.appendChild(btn_child2);

        let btn_child3 = document.createElement("p");
        btn_child3.className = "payload-author";
        btn_child3.innerHTML = "v" + payload_map[i].version + " &centerdot; " + payload_map[i].author;
        btn.appendChild(btn_child3);

        document.getElementById("payloads-list").appendChild(btn);
        document.getElementById("payloads-list").opacity = 0;

    }

}

function showToast(message) {
    const toastContainer = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;

    toastContainer.appendChild(toast);

    // Trigger reflow and enable animation
    toast.offsetHeight;

    toast.classList.add('show');

    setTimeout(() => {
        toast.classList.add('hide');
        toast.addEventListener('transitionend', () => {
            toast.remove();
        });
    }, 2000);
}
