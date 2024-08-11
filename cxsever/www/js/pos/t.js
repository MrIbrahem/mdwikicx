
async function get_files_api(title) {
    var url = 'https://mdwiki.toolforge.org/cx/get_html.php?' + jQuery.param({ "title": title })

    const options = {
        method: 'GET',
        dataType: 'json',
        // dispatcher: new Agent({ connect: { timeout: 60_000 } })
    };
    const response = await fetch(url, options);

    const data = await response.json();

    const segmentedContent = data.segmentedContent;

    return segmentedContent;
}

function get_files() {
    $("#load_files").show();

    var title = $("#title").val();
    (async () => {
        const oldtext = await get_files_api(title);

        $("#oldtext").val(oldtext);

        $("#load_files").hide();

    })();
}

async function fix_it_api(text) {

    const options = {
        headers: { "Content-Type": "application/json" },
        method: 'POST',
        dataType: 'json',
        body: JSON.stringify({ html: text }),
        // dispatcher: new Agent({ connect: { timeout: 60_000 } })
    };
    const response = await fetch('/textp', options);
    if (!response.ok) {
        console.error(response.statusText);
        return "";
    }
    const data = await response.json();

    const result = data.result;

    return result;
}
function fix_it() {
    $("#load_fixit").show();

    var text = $("#oldtext").val();
    if (!text) {
        $("#load_fixit").hide();
        $("#new").val("no text");
        return;
    }

    (async () => {
        const newtext = await fix_it_api(text);
        $("#new").val(newtext);
        $("#load_fixit").hide();

    })();
}
