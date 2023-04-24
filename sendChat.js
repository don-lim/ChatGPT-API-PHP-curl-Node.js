const https = require('https');

if (req.method === 'POST') {
    const url = 'https://api.openai.com/v1/chat/completions';
    const apiKey = 'sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'; // replace with your key

    const temperature = parseFloat(req.body.tempRange);
    const n = parseInt(req.body.numResponses);
    const prompt = req.body.prompt;

    const messages = [];

    if (req.body.systemRole) {
        messages.push({
            "role": "system",
            "content": req.body.systemRole
        });
    }

    if (req.body.assistantRole) {
        messages.push({
            "role": "assistant",
            "content": req.body.assistantRole
        });
    }

    if (req.body.prompt) {
        messages.push({
            "role": "user",
            "content": req.body.prompt
        });
    } else {
        res.setHeader('Content-Type', 'application/json');
        res.status(400).send({response: "Error: Prompt cannot be empty."});
        return;
    }

    const postData = JSON.stringify({
        "model": "gpt-3.5-turbo",
        "messages": messages,
        "max_tokens": 200,
        "temperature": temperature,
        "n": n
    });

    const options = {
        hostname: 'api.openai.com',
        port: 443,
        path: '/v1/chat/completions',
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + apiKey
        }
    };

    const req = https.request(options, (res) => {
        let response = '';

        res.on('data', (chunk) => {
            response += chunk;
        });

        res.on('end', () => {
            response = JSON.parse(response);
            res.setHeader('Content-Type', 'application/json');
            res.status(200).send({response});
        });
    });

    req.on('error', (error) => {
        console.error(error);
        res.setHeader('Content-Type', 'application/json');
        res.status(500).send({response: 'Error: ' + error.message});
    });

    req.write(postData);
    req.end();
}
