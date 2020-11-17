let table = document.querySelector("table");

let selectedRow, selectedRowID = 0;


//Elements
let form = document.querySelector(".definitions > form");
let btnSubmit = document.querySelector("#submitEdit");
let defID = document.querySelector("#defID");
let word_id = document.querySelector("#word_id");
let wordID = document.querySelector("#wordID");
let word_name = document.querySelector("#word_name");
let word_definition = document.querySelectorAll(".word_definition");


let firstRowSkipped = false;
for (let row of table.rows) {

    if (!firstRowSkipped) {
        firstRowSkipped = true;
        continue;
    }

    row.addEventListener("click", event => {
        console.log("Row Clicked");

        if (event.target.classList.contains("fas")) return;
        
        // Remove previous "selectedRow" class
        let previousItems = document.querySelectorAll(".selectedRow");
        if (previousItems.length > 0) {
            for (let element of previousItems) {
                element.removeAttribute("class");

                let i = document.querySelectorAll("td > i");
                i.forEach(element => {
                    element.remove();
                });
            }
        }


        selectedRow = event.target.parentElement;
        if (selectedRow.dataset.id !== selectedRowID) {
            selectedRow.classList.add("selectedRow");

            selectedRowID = selectedRow.dataset.id;

            let trash = document.createElement("i");
            trash.classList.add("fas");
            trash.classList.add("fa-trash-alt");
            trash.addEventListener("click", deleteData);

            // <i class="fas fa-pencil-alt"></i>

            let pencil = document.createElement("i");
            pencil.classList.add("fas");
            pencil.classList.add("fa-pencil-alt");
            pencil.addEventListener("click", editData);

            selectedRow.children[0].append(trash);
            selectedRow.children[0].append(pencil);


        } else { // Desselecionar na listView
            selectedRowID = 0;
        }

    })
}

function deleteData() {
    word_id.value = selectedRow.dataset.id;

    btnSubmit.name = "delete";
    form.action = "Actions/del.php";
    btnSubmit.click();
}


function editData() {
    word_id.value = selectedRow.dataset.id;
    btnSubmit.click();
}