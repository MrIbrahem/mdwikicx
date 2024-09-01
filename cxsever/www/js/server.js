var express = require("express");
var cors = require('cors');
var bodyParser = require('body-parser');
var u = require('./lib/d/u.js');
var pa = require('./lib/d/pa.js');

var app = express();

// app.use(cors({ origin: ['http://localhost:300']}));

app.use(cors())

app.use(bodyParser.json({ limit: '50mb' }));
app.use(bodyParser.urlencoded({ limit: '50mb', extended: false }));

app.get("/page/:title", function (req, res) {

	const title = req.params.title;
	pa.get_text_api_new(title, function (text) {
		const result = u.tet(text);
		res.send({
			title: title,
			result: result
		});
	});
});

app.get("/textwmcloud/:title", function (req, res) {
	const title = req.params.title;

	pa.get_text_api_wmcloud(title, function (text) {
		res.send({
			title: title,
			result: text
		});
	}
	);
});

app.get("/pagetext/:title", function (req, res) {
	const title = req.params.title;

	pa.get_text_api_new(title, function (text) {
		res.send({
			title: title,
			result: text
		});
	}
	);
});

app.post("/textp", (req, res) => {
	const sourceHtml = req.body.html;

	if (!sourceHtml || sourceHtml.trim().length === 0) {
		res.send({
			result: 'Content for translate is not given or is empty'
		});
		res.status(500).end();
		return;
	}
	try {
		const processedText = u.tet(sourceHtml);
		res.send({ result: processedText });
	} catch (error) {
		console.error(error);
		res.send({
			result: error.message
		});
		res.status(500).end();
	}
	// res.send(processedText);

});

app.get('/', (req, res) => {

	res.sendFile(__dirname + '/pos/index.html');
});


app.get('/f', (req, res) => {

	res.sendFile(__dirname + '/pos/fixed.html');
});

app.get('/js.js', (req, res) => {
	res.sendFile(__dirname + '/pos/js.js');
});

app.listen(process.env.PORT || 8000, function () {
	console.log("Node.js app is listening on port " + (process.env.PORT || 8000));
});

