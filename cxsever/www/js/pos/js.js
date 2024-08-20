
async function get_files_api_new(title, ty) {
    // var url = 'https://medwiki.toolforge.org/get_html/rest_v1_page.php?title=' + title

    const options = {
        method: 'GET',
        dataType: 'json',
    };
    const response = await fetch('/' + ty + '/' + title, options);

    const result = await response.json();

    return result.result;
}

function get_files() {
    $("#load_files").show();

    var title = $("#title").val();
    (async () => {
        const oldtext = await get_files_api_new(title, 'pagetext');

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


function get_Fixed() {
    $("#load_Fixed").show();

    var title = $("#title").val();
    (async () => {
        const oldtext = await get_files_api_new(title, "page");

        $("#fixed_text").val(oldtext);

        $("#load_Fixed").hide();

    })();
}
