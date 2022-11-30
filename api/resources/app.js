$(function () {
    const baseURL = "http://api.local";
    const itemInput = $('#item-input');
    const add = $('#add');
    const itemsContainer = $('#items-container');

    // on loading the app
    $.ajax({
        url: baseURL + "/items",
        type: "GET",
        success: function (data) {
            data.body.forEach(item => {
                console.log(item.completed);
                itemsContainer.append(`
                <div data-id="${item.id}" class="item w-25 justify-content-between align-items-center mb-3 p-1 border-bottom ${item.completed == 1 ? "completed" : "" }">
                    <input class="form-check-input" type="checkbox" ${item.completed == 1 ? "checked" : "" }>
                    <p class="m-0">${item.name}</p>
                    <button class="btn btn-danger">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>`
                );

                $(`div[data-id="${item.id}"] input`).change(function (e) {
                    $(this).parent().toggleClass('completed');
                    $.ajax({
                        type: "PUT",
                        url: baseURL + "/items/update",
                        data: JSON.stringify({
                            id: item.id
                        }),
                        dataType: "application/json",
                        success: function (response) {
                            console.log(response)
                        },
                        error: function (e) {
                            console.log(e)
                        }
                    });
                });

                $(`div[data-id="${item.id}"] button`).click(function (e) {
                    $.ajax({
                        type: "DELETE",
                        url: baseURL + "/items/delete",
                        data: JSON.stringify({
                            id: item.id
                        }),
                        dataType: "application/json",
                        success: function (response) {
                            console.log(response)
                        },
                        error: function (e) {
                            console.log(e)
                        }
                    });
                    $(this).parent().hide('slow', function () {
                        $(this).remove();
                    });

                });
            });

        }
    });

    // to automatically focus on the input without the user action to click on the input to start typing
    itemInput.focus();

    let counter = 1;

    add.click(function () {
        const itemContent = itemInput.val();

        if (itemContent == "") {
            alert("You need to add item");
            return;
        }

        // check if the item is already existed in the app. 
        // Get all items
        let items = $('.item p');
        let additionSwitch = false;
        // loop through all items
        items.each(function (i) {
            // check if the current item in the loop equals the new item.
            if ($(this).text() == itemContent) {
                alert('This item is already exists.');
                additionSwitch = true;
            }
        });

        if (additionSwitch) {
            return;
        }

        $.ajax({
            type: "POST",
            url: baseURL + "/items/create",
            data: JSON.stringify({
                name: itemContent
            }),
            dataType: "application/json",
            success: function (response) {
                console.log('done')
                itemsContainer.append(`
                <div data-id="${response.id}" class="item w-25 justify-content-between align-items-center mb-3 p-1 border-bottom">
                    <input class="form-check-input" type="checkbox">
                    <p class="m-0">${response.name}</p>
                    <button class="btn btn-danger">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
                `);

                $(`div[data-id="${response.id}"] input`).change(function (e) {
                    $(this).parent().toggleClass('completed');
                    $.ajax({
                        type: "PUT",
                        url: baseURL + "/items/update",
                        data: JSON.stringify({
                            id: response.id
                        }),
                        dataType: "application/json",
                        success: function (response) {
                            console.log(response)
                        },
                        error: function (e) {
                            console.log(e)
                        }
                    });
                });

                $(`div[data-id="${response.id}"] button`).click(function (e) {
                    $(this).parent().hide('slow', function () {
                        $(this).remove();
                    });
                    $.ajax({
                        type: "DELETE",
                        url: baseURL + "/items/delete",
                        data: JSON.stringify({
                            id: item.id
                        }),
                        dataType: "application/json",
                        success: function (response) {
                            console.log(response)
                        },
                        error: function (e) {
                            console.log(e)
                        }
                    });
                });

                itemInput.val('');
                itemInput.focus();
            },
            
        });




    });



});