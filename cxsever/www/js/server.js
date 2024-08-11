var express = require("express");
var cors = require('cors');
var bodyParser = require('body-parser');
var u = require('./lib/d/u.js');
var pa = require('./lib/d/pa.js');

var app = express();

app.use(cors({
	origin: ['http://localhost:300'] // Replace with your actual frontend origin
}));

app.use(bodyParser.json({ limit: '50mb' }));
app.use(bodyParser.urlencoded({ limit: '50mb', extended: false }));

const sourceHTML = `
<body>
	<section data-mw-section-id="0">
	<p id="mwAb">Paragraph <b>bold</b> <a href="/wiki/Title">Title</a>.</p>
	</section>
	<section data-mw-section-id="1">
	<h3>Heading</h3>
	<table><tr><td>data</td></tr></table>
	<div id="mwAc">Content<div>innerdiv</div></div>
	</section>
	<section data-mw-section-id="2">
	<p>Content<div>Div in paragraph</div></p>
	<ol><li>Item</li><li>Item</li></ol></section>
	<section data-mw-section-id="3">
	<div typeof="mw:Transclusion" about="#mwt1" data-mw="{}">Block template</div>
	</section>
	<section data-mw-section-id="4">
	<span typeof="mw:Transclusion" about="#mwt2" data-mw="{}">Some text content</span>
	<table about="#mwt2"><tr><td>used value</td></tr></table>
	</section>
	<section data-mw-section-id="5">
	<p>An inline <span typeof="mw:Transclusion" about="#mwt3" data-mw="{}">template</span></p>
	</section>
	<section data-mw-section-id="6">
	<span typeof="mw:Transclusion" about="#mwt4" data-mw="{}">Template 4: Some text content</span>
	<table about="#mwt4"><tr><td>Template 4: value</td></tr></table>
	<span typeof="mw:Transclusion" about="#mwt5" data-mw="{}">Template 5: Some text content</span>
	<table about="#mwt5"><tr><td>Template 5: value</td></tr></table>
	</section>
	<figure class="mw-default-size mw-halign-right" id="mweA" typeof="mw:File/Thumb">
	<a href="./File:PriestleyFuseli.jpg" id="mweQ">
	<img alt="Alt text" data-file-height="587" data-file-type="bitmap" data-file-width="457" height="218" id="mweg" resource="./File:PriestleyFuseli.jpg" src="//upload.wikimedia.org/wikipedia/commons/thumb/4/4a/PriestleyFuseli.jpg/170px-PriestleyFuseli.jpg" srcset="//upload.wikimedia.org/wikipedia/commons/thumb/4/4a/PriestleyFuseli.jpg/340px-PriestleyFuseli.jpg 2x, //upload.wikimedia.org/wikipedia/commons/thumb/4/4a/PriestleyFuseli.jpg/255px-PriestleyFuseli.jpg 1.5x" width="170" /></a>
	<figcaption id="mwew">
	<a href="./Joseph_Priestley" id="mwfA" rel="mw:WikiLink" title="Joseph Priestley">Joseph Priestley</a> is usually given priority in the discovery.
	</figcaption>
	</figure>
	<dl id="mwAW8">
	<dd id="mwAXA">3 Fe + 4 H<sub id="mwAXE">2</sub></dd>
	</dl>
	<link href="./Category:Category1" id="mwCKQ" rel="mw:PageProp/Category" />
	<link rel="mw:PageProp/Category" href="./Category:All_stub_articles" about="#mwt8" typeof="mw:Transclusion" data-mw='{"parts":[{"template":{"target":{"wt":"nervous-system-drug-stub","href":"./Template:Nervous-system-drug-stub"},"params":{},"i":0}}]}'
	id="mwJg" />
	<link rel="mw:PageProp/Category" href="./Category:Nervous_system_drug_stubs" about="#mwt8" />
	<table class="plainlinks stub" role="presentation" style="background:transparent" about="#mwt8" id="mwJw">
	<tbody></tbody>
	</table>
	<span id="empty_inline_annotation_transclusion" about="#mwt335" typeof="mw:Transclusion" data-mw='{"parts":[{"template":{"target":{"wt":"anchor","href":"./Template:Anchor"},"params":{"1":{"wt":"partial pressure"}},"i":0}}]}'></span>
	<link rel="mw:PageProp/Category" href="./Category:Wikipedia_indefinitely_move-protected_pages#Oxygen" about="#mwt3" typeof="mw:Transclusion" data-mw='{"parts":[{"template":{"target":{"wt":"pp-move-indef","href":"./Template:Pp-move-indef"},"params":{},"i":0}}]}' id="mwBg" />
	<section data-mw-section-id="61">
	<div role="navigation" class="navbox" about="#mwt61" typeof="mw:Transclusion" data-mw="{}">
	Section to be removed from output based on the navbox class
	</div>
	<link rel="mw:PageProp/Category" href="./Category:Food_preparation" about="#mwt61">
	<span about="#mwt61">Fragment 2</span>
	</section>
	</body>`;

app.get("/page/:title", function (req, res) {

	const title = req.params.title;
	const text = pa.get_html_text(title);
	if (!text) {
		res.status(500).end('text is empty');
		return;
	}
	const result = u.tet(text);
	res.send({
		title: title,
		result: result
	});
});

app.get("/pagetext/:title", function (req, res) {

	const title = req.params.title;
	const result = pa.get_html_text(title);
	res.send({
		title: title,
		result: result
	});
});

app.get("/text/", function (req, res) {

	const html_result = u.tet(sourceHTML);
	const result = html_result;
	res.send({
		result: result
	});

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


app.get('/t', (req, res) => {

	res.sendFile(__dirname + '/pos/t.html');
});

app.get('/t.js', (req, res) => {

	res.sendFile(__dirname + '/pos/t.js');
});

app.listen(process.env.PORT || 8000, function () {
	console.log("Node.js app is listening on port " + (process.env.PORT || 8000));
});

