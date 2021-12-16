const MAX_SNOWFLAKES = 200;
const SNOWFLAKE_HORIZONTAL_MOVEMENT = 10;
const SNOWFLAKE_HORIZONTAL_SPEED = 1;
const SNOWFLAKE_VERTICAL_SPEED = 1;
const ADD_SNOWFLAKE_FREQ = 800;
const MOVE_SNOWFLAKE_FREQ = 50;
const REMOVE_SNOWFLAKE_FREQ = 1000;
const Z_INDEX = 10;

let snowflakes = [];
let snowflakeAdder;
let snowflakeMover;
let snowflakeRemover;
let lastMoveUpdate;
let lastAddUpdate;

function initOptions(options) {
    return Object.assign({
        background: 'rgba(150,150,200,0.8)',
        borderRadius: '10px',
        width: '5px',
        height: '5px',
        boxShadow: '1px 1px .5px rgba(0,0,0,0.8)',
        addSnowflakeFrequency: ADD_SNOWFLAKE_FREQ,
        moveSnowflakeFrequency: MOVE_SNOWFLAKE_FREQ,
        removeSnowFlakeFrequency: REMOVE_SNOWFLAKE_FREQ,
        horizontalSpeed: SNOWFLAKE_HORIZONTAL_SPEED,
        horizontalMovement: SNOWFLAKE_HORIZONTAL_MOVEMENT,
        verticalSpeed: SNOWFLAKE_VERTICAL_SPEED,
        maxSnowflakes: MAX_SNOWFLAKES,
        zIndex:Z_INDEX,
    }, options);
}

function addSnowflake(options) {
    if (snowflakes.length >= options.maxSnowflakes || isAddUpdaterInactive(options)) {
        lastAddUpdate = Date.now();
        return;
    }

    let snowflake = document.createElement('i');
    initCustomOptions(snowflake, options);
    initRandomOptions(snowflake, options);

    document.body.appendChild(snowflake);
    snowflakes.push(snowflake);
    lastAddUpdate = Date.now();
}

function initCustomOptions(snowflake, options) {
    snowflake.style.background = options.background;
    snowflake.style.borderRadius = options.borderRadius;
    snowflake.style.width = options.width;
    snowflake.style.height = options.height;
    snowflake.style.boxShadow = options.boxShadow;
    snowflake.style.zIndex = options.zIndex;
    snowflake.style.top = '-' + options.height;
    snowflake.style.position = 'fixed';
}

function initRandomOptions(snowflake, options) {
    snowflake.initLeft = window.innerWidth * Math.random();
    snowflake.style.left = snowflake.initLeft + 'px';
    snowflake.xOffset = (Math.random() < 0.5 ? -1 : 1) * Math.floor(options.horizontalMovement * Math.random());
    if (snowflake.xOffset > 0) {
        snowflake.direction = 'left';
    } else {
        snowflake.direction = 'right';
    }
}

function moveSnowflakes(options) {
    if (isMoveUpdaterInactive(options)) {
        lastMoveUpdate = Date.now();
        return;
    }

    for (let snowflake of snowflakes) {
        moveSnowflakeVertically(snowflake, options);
        moveSnowflakeHorizontally(snowflake, options);
    }

    lastMoveUpdate = Date.now();
}

function isAddUpdaterInactive(options) {
    let now = Date.now();

    return now - lastAddUpdate > options.addSnowflakeFrequency;
}

function isMoveUpdaterInactive(options) {
    let now = Date.now();

    return now - lastMoveUpdate > options.addSnowflakeFrequency;
}

function moveSnowflakeVertically(snowflake, options) {
    snowflake.style.top = (parseInt(snowflake.style.top.replace('px', '')) + options.verticalSpeed) + 'px';
}

function moveSnowflakeHorizontally(snowflake, options) {
    if (options.horizontalMovement > 0) {
        if (snowflake.direction === 'right') {
            snowflake.xOffset += options.horizontalSpeed;

            if (snowflake.xOffset >= options.horizontalMovement) {
                snowflake.direction = 'left';
            }
        } else {
            snowflake.xOffset -= options.horizontalSpeed;

            if (snowflake.xOffset <= -options.horizontalMovement) {
                snowflake.direction = 'right';
            }
        }

        snowflake.style.left = (snowflake.initLeft + snowflake.xOffset) + 'px';
    }
}

function removeSnowflakes() {
    let activeSnowflakes = [];
    for (let snowflake of snowflakes) {
        if (parseInt(snowflake.style.top.replace('px', '')) < window.innerHeight) {
            activeSnowflakes.push(snowflake);
        } else {
            document.body.removeChild(snowflake);
        }
    }

    snowflakes = activeSnowflakes;
}
let init = function (options) {
    options = initOptions(options);

    if (snowflakeAdder) {
        clearInterval(snowflakeAdder);
        clearInterval(snowflakeMover);
        clearInterval(snowflakeRemover);
    }

    snowflakeAdder = setInterval(() => addSnowflake(options), options.addSnowflakeFrequency);
    snowflakeMover = setInterval(() => moveSnowflakes(options), options.moveSnowflakeFrequency);
    snowflakeRemover = setInterval(() => removeSnowflakes(options), options.removeSnowFlakeFrequency);
};

exports.init = init;

exports.default = {
    init: init
};