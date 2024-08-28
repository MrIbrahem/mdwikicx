
async function get_text_api_new(title, ty) {
    // var url = 'https://medwiki.toolforge.org/get_html/rest_v1_page.php?title=' + title

    const options = {
        method: 'GET',
        dataType: 'json',
    };
    const response = await fetch('/' + ty + '/' + title, options);

    const result = await response.json();

    return result.result;
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

function do_seconds(start_time, id) {
    const time = new Date().getSeconds() - start_time;

    $(id).text( "in " + time + " Seconds");
}

function get_text(ty) {
    var start_time = new Date().getSeconds();
    $("#load_" + ty).show();

    var title = $("#title_"+ty).val();
    (async () => {
        const oldtext = await get_text_api_new(title, ty);
        $("#old_" + ty).val(oldtext);
        $("#load_" + ty).hide();
        do_seconds(start_time, "#time_" + ty);

    })();
}
function fix_it() {
    var start_time = new Date().getSeconds();
    $("#load_fixit").show();

    var text = $("#old_pagetext").val();
    if (!text) {
        $("#load_fixit").hide();
        $("#new").val("no text");
        return;
    }

    (async () => {
        const newtext = await fix_it_api(text);
        $("#new").val(newtext);
        $("#load_fixit").hide();
        do_seconds(start_time, "#time_fixit");

    })();
}

function get_Fixed() {
    var start_time = new Date().getSeconds();
    $("#load_Fixed").show();

    var title = $("#title").val();
    (async () => {
        const oldtext = await get_text_api_new(title, "page");

        $("#fixed_text").val(oldtext);

        $("#load_Fixed").hide();
        do_seconds(start_time, "#time_Fixed");

    })();
}
