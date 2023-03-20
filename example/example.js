// Parameters to use for getting event data and prediction data
let method = "MAG4_LOS_FEr_REGIONS";
let start = "2023-03-01T00:00:00Z";
let stop = "2023-03-01T23:59:59Z";

// Get event data
async function getEvents() {
    let response = await fetch("https://api.helioviewer.org/?action=getEvents&startTime="+start+"&eventType=AR");
    let events = await response.json();
    return events;
}

async function getPredictions() {
    let response = await fetch("http://localhost:8000/scoreboard.php?method="+method+"&start="+start+"&stop="+stop);
    let predictions = await response.json();
    return predictions;
}

function crossReferencePredictionsAndEvents(events, predictions) {
    let associations = [];
    // HAPI data is returned as an array, so we need the index of the region_id in for the data array.
    let region_id_index = predictions.parameters.findIndex((e) => e.name == "NOAARegionId")

    // Iterate over each prediction and find the associated event.
    predictions['data'].forEach(prediction => {
        let noaa_region_id = prediction[region_id_index];
        let found_event = null;
        // Iterate over each event to see if it has the same NOAA ID as the prediction
        for (let i = 0; i < events.length; i++) {
            let event = events[i];
            // Why CCMC? Why is the NOAA number offset by 10000?
            // If the event has a noaa num and it matches the prediction, then add this pair to the associations array.
            if (event.hasOwnProperty('ar_noaanum') && (event.ar_noaanum - 10000) == noaa_region_id) {
                found_event = event;
                break;
            }
        }
        // Push the event that was found (or null) into the associations array.
        associations.push({
            event: found_event,
            prediction: prediction
        });
    });
    return associations;
}

function renderTemplate(template, model) {
    let html = template;
    model.forEach((value, index) => {
        html = html.replace("{{"+index+"}}", value);
    });
    return html;
}

async function displayAssociations(associations) {
    let container = document.getElementById("associations");
    let response = await fetch("http://localhost:8000/models/prediction.html");
    let template = await response.text();
    associations.forEach(association => {
        let event = association.event;
        let prediction = association.prediction;
        if (event) {
            let html = "<div><p>Event: " + event.kb_archivid + "</p>" + renderTemplate(template, prediction) + "</div>";
            container.innerHTML += html;
        }
    });
}

async function main() {
    let events = await getEvents();
    let predictions = await getPredictions();
    let associations = crossReferencePredictionsAndEvents(events, predictions);
    console.log(associations);
    displayAssociations(associations);
}

main();