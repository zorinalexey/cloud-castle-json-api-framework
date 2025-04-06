const WebSocket = require('ws');
const fs = require('fs');
const {format} = require('date-fns');
const port = 3000;
const wsServer = new WebSocket.Server({port: port});
const channels = {};


function ensureLogDirectoryExists(directory) {
    if (!fs.existsSync(directory)) {
        fs.mkdirSync(directory, {recursive: true});
    }
}

function logToFile(message) {
    const timestamp = format(new Date(), 'yyyy-MM-dd HH:mm:ss');
    const y = format(new Date(), 'yyyy');
    const m = format(new Date(), 'MM');
    const d = format(new Date(), 'dd');
    const logDir = `./logs/${y}/${m}/${d}`;
    const logMessage = `[${timestamp}] ${message}\n`;
    ensureLogDirectoryExists(logDir);

    fs.appendFile(`${logDir}/server.log`, logMessage, (err) => {
        if (err) {
            console.error('Ошибка записи в файл лога:', err);
        }
    });
}

function handleMessage(ws, message) {
    try {
        const data = JSON.parse(message);
        data.date = format(new Date(), 'yyyy-MM-dd HH:mm:ss');

        if (data.data === 'ping') {
            data.data = 'pong';
            ws.send(JSON.stringify(data));
            logToFile('Отправлено сообщение "pong" отправителю');
            return;
        }

        const channel = data.channel;
        if (data.subscribe) {
            subscribeToChannels(ws, data.subscribe);
        }

        if (channel) {
            broadcastToChannel(channel, data);
        }
    } catch (error) {
        logError(error);
    }
}

function subscribeToChannels(ws, subscriptions) {
    for (const newChannel of subscriptions) {
        if (!channels[newChannel]) {
            channels[newChannel] = [];
        }
        if (!channels[newChannel].includes(ws)) {
            channels[newChannel].push(ws);
            logToFile(`Клиент подписался на канал: ${newChannel}`);
        }
    }
}

function broadcastToChannel(channel, data) {
    if (!channels[channel]) {
        channels[channel] = [];
    }

    channels[channel].forEach((client) => {
        if (client.readyState === WebSocket.OPEN) {
            client.send(JSON.stringify(data));
            logToFile(`Сообщение отправлено в канал: ${channel}, данные: ${JSON.stringify(data)}`);
        }
    });
}

function handleClientDisconnect(ws) {
    for (const channel in channels) {
        channels[channel] = channels[channel].filter(client => client !== ws);
    }
    logToFile('Клиент отключился');
}

wsServer.on('connection', (ws) => {
    ws.on('message', (message) => handleMessage(ws, message));
    ws.on('close', () => handleClientDisconnect(ws));
});

logToFile('WebSocket сервер запущен на порту ' + port);