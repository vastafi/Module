$(document).ready(function () {
    showCart();

    $(document).on('click', '#toggle-cart', function () {
        showCart();
    });

});

function showCart() {
    $.getJSON("http://localhost:8000/api/v1/cart", function (data) {
        readItemsTemplate(data);

    });
}

function readItemsTemplate(data) {

    var read_items_html = ``;
    var total = 0;

    if (data.length > 0) {
        read_items_html += `
        <table class='cart-table table'>
            <tr>
<!--             <th class='w-5-pct bg-dark text-white' colspan="2" style="text-align: center">Product</th>-->
                <th class='w-5-pct bg-dark text-white' style="text-align: center">Product</th>
                <th class='w-5-pct  bg-dark text-white' style="text-align: center">Price</th>
                <th class='w-5-pct bg-dark text-white' style="text-align: center">Amount</th>
                <th class='w-5-pct bg-dark text-white' style="text-align: center">Total</th>
                <th class='w-5-pct bg-dark text-white' style="text-align: center">Action</th>
             </tr>`;
    }

    var formatter = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    });

    data.forEach(cartItem => {

        // var icon = JSON.parse(cartItem['product']['imgPath']);

        total += cartItem['product']['price'] * cartItem['amount'];

        read_items_html += `
        <tr>
<!--            <td style="vertical-align: middle;text-align: center"><img src="assets/main/img/250x200.png" alt="img" width="100" height="100"></td>&ndash;&gt;-->
            <td style="vertical-align: middle;text-align: center">` + cartItem['product']['name'] + `</td>
            <td style="vertical-align: middle;text-align: center">` + formatter.format(cartItem['product']['price']) + `</td>
            <td style="vertical-align: middle;text-align: center">` + cartItem['amount'] + `</td>
            <td style="vertical-align: middle;text-align: center">` + formatter.format(cartItem['amount'] * cartItem['product']['price']) + `</td>
            <td style="vertical-align: middle;text-align: center"><button class="delete_cart btn btn-danger"  data-prod-code="` + cartItem['product']['code'] + `">
             <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                     fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                    <path fill-rule="evenodd"
                                                          d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                </svg>
</button></td>
        </tr>
        `;
    });

    if (window.location.href === 'http://localhost:8000/cart') {
        $("#cart-container").css("overflow-y", "hidden").css("height", "auto");
    }

    if (total === 0) {
        read_items_html += `<p class="empty-cart">Your cart is empty. But it's easy to fix! Go shopping!</p>`;
    } else {
        read_items_html += `
            <tr>
                <td style="vertical-align: middle;text-align: right" colspan="3"><b>Total : </b></td>
                <td style="vertical-align: middle;text-align: center"><b>` + formatter.format(total) + ` </b></td>
                <td></td>
            </tr>
        </table>`;
    }

    $(".cart-content").html(read_items_html);
}
