<?php

require_once("../../app/v1/connection-branch-admin.php");

require_once("../common/header.php");

require_once("../common/navbar.php");

require_once("../common/sidebar.php");

?>


<style>
    * {
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .container.family-tree {
        padding: 0px;
        font-size: 16px;
        margin: 24px auto 0px;
        overflow: auto;
        max-width: 100%;
        transform: translateY(-8%) scale(0.8);
    }

    .container.family-tree ul.hidden {
        opacity: 0;
        visibility: hidden;
        transform: scale(0);
    }

    .container.family-tree ul {
        display: flex;
        opacity: 1;
        visibility: visible;
        transform: scale(1);
        transition: 300ms visibility, 300ms transform, 300ms opacity;
    }

    .container.family-tree ul li {
        flex: 1;
        text-align: center;
        padding: 0 10px;
        position: relative;
    }

    .container.family-tree ul li a {
        text-decoration: none;
        color: #000;
    }

    .container.family-tree ul:first-child {
        opacity: 1;
        visibility: visible;
        transform: scale(1);
    }

    .container.family-tree ul li input {
        display: none;
    }

    .container.family-tree ul li::before {
        content: "";
        position: absolute;
        top: -11px;
        left: 0;
        right: 0;
        height: 2px;
        background: #b3b3b3;
    }

    .container.family-tree ul li:first-child::before {
        left: 46%;
    }

    .container.family-tree ul li:last-child::before {
        right: 49%;
    }

    li.parent-li::before {
        display: none;
    }

    .container.family-tree ul li label {
        width: auto;
        height: auto;
        padding: 25px 20px;
        border-radius: 5px;
        margin-bottom: 23px;
        position: relative;
        white-space: nowrap;
        box-sizing: border-box;
        user-select: none;
        /* cursor: pointer; */
    }


    .container.family-tree ul li label .card {
        /* width: clamp(26vw, 50%, 10vw); */
        height: 100%;
        background: #f7f7f7;
        box-shadow: 20px 16px 12px -8px #00000042;
        transition-duration: 0.2s;
        margin-bottom: 0;
    }

    .container.family-tree ul li label .card::before {
        content: '';
        position: absolute;
        width: 3px;
        height: 33px;
        left: 147px;
        top: -34px;
        background-color: #b3b3b3;
    }

    .container.family-tree ul li label .card:hover {
        box-shadow: 20px 19px 12px -19px #00000042;
    }

    .container.family-tree ul li label .card .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #fff;
        z-index: 999;
        top: 0;
        border-bottom-left-radius: 0;
    }

    .container.family-tree ul li label .card .card-header h2 {
        color: #fff;
        margin-bottom: 0;
    }

    .container.family-tree ul li label .card .card-body {
        padding-left: 2rem;
        padding-right: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
    }

    .container.family-tree ul li label .card .card-body span {
        font-weight: 600;
    }

    .container.family-tree ul li label .card .card-body .amount-sec {
        display: grid;
        place-items: center;
        height: 4rem;
        justify-content: flex-start;
    }

    .container.family-tree ul li label .card .card-body .body-block {
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-align: left;
        padding-top: 2rem;
        gap: 2rem;
    }

    .vertical-text-section a {
        transform: rotate(90deg);
    }

    .created-date {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .created-date i {
        background: #003060;
        padding: 10px;
        border-radius: 50%;
        color: #fff;
    }

    .container.family-tree ul li label .card .card-body span.amount {
        font-size: 25px;
    }

    .container.family-tree ul li label::after,
    .container.family-tree ul li label::before {
        content: "";
        width: 2px;
        position: absolute;
        top: calc(-1em - 0px);
        height: 37px;
        left: 50%;
        transition: 300ms all;
        background: #b3b3b3;
    }

    .container.family-tree ul li label::after {
        top: auto;
        bottom: calc(-1em - 1px);
    }

    /* .container.family-tree ul li label:last-child::after {
            display: none;
        }

        .container.family-tree>ul>li>label::before {
            display: none;
        } */

    .container.family-tree ul li input:checked+label {
        color: darkorange;
    }

    .container.family-tree ul li input:checked+label::after {
        height: 1em;
    }

    .container.family-tree ul li input:checked+label+ul {
        opacity: 1;
        visibility: visible;
        transform: scale(1);
    }

    label.parent-label::before {
        content: "";
        display: none;
    }

    li.parent-label {
        display: none;
    }

    .onprocess {
        filter: blur(5px) grayscale(1);
    }

    /* .parent-card::before {
        display: none;
    } */

    .vertical-text-section {
        background: #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0;
        position: absolute;
        top: 0;
        left: 0;
        width: 46px;
        height: 152px;
    }

    .hori-text-section {
        position: relative;
        left: 0;
        padding-left: 3rem;
    }

    .onprocess .card-header .docNo,
    .onprocess .card-header p,
    .onprocess .amount-sec,
    .onprocess .details,
    .onprocess .created-date,
    .onprocess .vertical-text-section span {
        background-color: #cccccc;
        background-image: linear-gradient(90deg, rgb(255 255 255), rgb(255 255 255 / 50%), rgb(255 255 255 / 47%));
        background-size: 40px 100%;
        background-repeat: no-repeat;
        background-position: left -40px top 0;
        -webkit-animation: shine 0s ease infinite;
        animation: shine 0s ease infinite;
        padding: 0 1rem;
        border-radius: 12px;
    }

    .onprocess .hori-text-section {
        position: relative;
        left: -64px;
        max-width: 90%;
    }

    .onprocess .hori-text-section .body-block {
        display: flex;
        align-items: center;
        justify-content: center !important;
        text-align: left;
        padding-top: 2rem;
        gap: 0.5rem !important;
    }

    .onprocess .hori-text-section .body-block div {
        width: 50%;
    }

    .onprocess .vertical-text-section {
        padding: 0 39px;
    }

    .party-blocks {
        display: flex;
        flex-direction: column;
        justify-content: center;
        margin: 20px 0 5px;
        background: #fff;
        padding: 2rem 25px;
        box-shadow: 5px 2px 8px -2px #00000040;
        max-width: 95%;
        position: sticky;
        top: 3rem;
        left: 11rem;
        right: 1rem;
        border-radius: 12px;
        font-size: 0.8rem;
        z-index: 999;
    }

    .party-blocks input[type="search"] {
        max-width: 50%;
        margin: 0.5rem auto;
    }

    .party-blocks .name {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin: 1.5rem 0 0;
    }

    .party-blocks .name span {
        border-right: 1px solid #ccc;
        padding-right: 1.5rem;
        padding-left: 1.5rem;
    }

    .party-blocks .name span:last-child {
        border: none;
    }

    .main-search {
        width: 450px;
        margin: 0px auto;
        text-align: center;
    }

    .main-search form {
        width: 450px;
        height: 50px;
        position: relative;
    }

    .main-search form input {
        width: 100%;
        height: 100%;
        padding: 0 50px 0 20px;
        border-radius: 25px;
        border: 1px solid #ccc;
        outline: none;
        box-sizing: border-box;
        font-size: 0.8rem;
    }

    .main-search button.search-button {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        top: 0;
        right: 0;
        overflow: hidden;
        border-radius: 50%;
        background-color: #003060;
        text-decoration: none;
        border: 0;
    }

    .main-search button.search-button .icon {
        display: flex;
        transition: all 0.6s ease;
    }

    .main-search button.search-button i {
        color: #fff;
        font-size: 1rem;
    }

    .main-search button.search-button .icon:after {
        width: 12px;
        height: 2px;
        display: block;
        content: "";
        position: absolute;
        bottom: -5px;
        right: -8px;
        background-color: #607d8b;
        transform: rotate(45deg);
        transition: all 0.6s ease;
    }

    .main-search button.search-button .icon .clear {
        width: 100%;
        height: 100%;
        line-height: 12px;
        display: block;
        text-align: center;
        color: #607d8b;
        font-size: 0;
        transition: all 0.6s ease;
    }

    .main-search button.search-button.typed .icon {
        width: 60px;
        height: 60px;
        border-radius: 50px;
        border-width: 6px;
        top: -11px;
        left: -11px;
    }

    .main-search button.search-button.typed .icon:after {
        width: 52px;
        height: 8px;
        bottom: -20px;
        right: -30px;
    }

    .main-search button.search-button.typed .icon .clear {
        line-height: 58px;
        font-size: 22px;
    }


    .OuterBorderRippleAnimation {
        width: 100%;
        height: auto;
        padding: 50px;
        display: flex;
        justify-content: space-around;
        align-items: center;
    }

    .bdr-ripple-ani-btn {
        display: block;
        background: #0066a9;
        width: 30px;
        height: 30px;
        line-height: 3;
        text-align: center;
        border-radius: 100%;
        box-sizing: border-box;
        color: #666;
        animation: at-ripple 0.6s linear infinite;
        overflow: hidden;
        border: 0;
        color: #fff !important;
    }

    .minus a.bdr-ripple-ani-btn {
        background: red;
    }

    .bdr-ripple-ani-btn.pink {
        background: #ff4081;
        color: white;
        animation: at-ripple-pink 0.6s linear infinite;
    }

    .bdr-ripple-ani-btn.two {
        right: 300px;
        z-index: 2;
        /*   position: fixed; */
        /*   bottom: 50px; */
    }

    .bdr-ripple-ani-btn i {
        transform: rotate(0deg);
        transition: 0.5s ease;
    }

    .bdr-ripple-ani-btn:hover i {
        transform: rotate(180deg);
    }

    @-webkit-keyframes at-ripple {
        0% {
            box-shadow: 0 4px 10px rgba(102, 102, 102, 0.1),
                0 0 0 0 rgba(102, 102, 102, 0.1), 0 0 0 5px rgba(102, 102, 102, 0.1),
                0 0 0 10px rgba(102, 102, 102, 0.1);
        }

        100% {
            box-shadow: 0 4px 10px rgba(102, 102, 102, 0.1),
                0 0 0 5px rgba(102, 102, 102, 0.1), 0 0 0 10px rgba(102, 102, 102, 0.1),
                0 0 0 20px rgba(102, 102, 102, 0);
        }
    }

    @keyframes at-ripple {
        0% {
            box-shadow: 0 4px 10px rgba(102, 102, 102, 0.1),
                0 0 0 0 rgba(102, 102, 102, 0.1), 0 0 0 5px rgba(102, 102, 102, 0.1),
                0 0 0 10px rgba(102, 102, 102, 0.1);
        }

        100% {
            box-shadow: 0 4px 10px rgba(102, 102, 102, 0.1),
                0 0 0 5px rgba(102, 102, 102, 0.1), 0 0 0 10px rgba(102, 102, 102, 0.1),
                0 0 0 20px rgba(102, 102, 102, 0);
        }
    }

    /* Pink Animate */
    @-webkit-keyframes at-ripple-pink {
        0% {
            box-shadow: 0 4px 10px rgba(255, 65, 130, 0.1),
                0 0 0 0 rgba(255, 65, 130, 0.1), 0 0 0 5px rgba(255, 65, 130, 0.1),
                0 0 0 10px rgba(255, 65, 130, 0.1);
        }

        100% {
            box-shadow: 0 4px 10px rgba(255, 65, 130, 0.1),
                0 0 0 5px rgba(255, 65, 130, 0.1), 0 0 0 10px rgba(255, 65, 130, 0.1),
                0 0 0 20px rgba(255, 65, 130, 0);
        }
    }

    @keyframes at-ripple-pink {
        0% {
            box-shadow: 0 4px 10px rgba(255, 65, 130, 0.1),
                0 0 0 0 rgba(255, 65, 130, 0.1), 0 0 0 5px rgba(255, 65, 130, 0.1),
                0 0 0 10px rgba(255, 65, 130, 0.1);
        }

        100% {
            box-shadow: 0 4px 10px rgba(255, 65, 130, 0.1),
                0 0 0 5px rgba(255, 65, 130, 0.1), 0 0 0 10px rgba(255, 65, 130, 0.1),
                0 0 0 20px rgba(255, 65, 130, 0);
        }
    }

    .plus {
        z-index: 9999;
        position: absolute;
        right: 0;
        top: -20px;
        width: 26px;
    }

    .plus:hover .tooltip {
        visibility: visible;
        opacity: 1;
    }

    .minus {
        z-index: 9999;
        position: absolute;
        left: 0;
        bottom: -20px;
        width: 26px;
    }

    .minus:hover .tooltip {
        visibility: visible;
        opacity: 1;
    }

    .tooltip {
        position: absolute;
        bottom: 6px;
        right: 65px;
        display: table;
        visibility: hidden;
        opacity: 0;
        transition: 0.5s;
        white-space: nowrap;
    }

    .tooltip p {
        color: #fff;
        background: rgba(51, 51, 51, 0.5);
        display: table-cell;
        vertical-align: middle;
        padding: 10px;
        border-radius: 3px;
    }

    .tooltip i {
        display: table-cell;
        vertical-align: middle;
        color: #333;
        opacity: 0.5;
    }

    .glow-shadow {
        background: #fff;
        width: 100px;
        height: 100px;
        /*     left:50px; */
        /*     margin-left:50px; */
        /*     margin-top:15%; */
        border-radius: 50%;
        -webkit-animation: throb 1.5s infinite ease-in-out;
        animation: glow 1.5s infinite ease-in-out;
    }

    @-webkit-keyframes glow {
        0% {
            -webkit-box-shadow: 0 0 50px 50px rgba(50, 160, 50, 0.9);
        }

        50% {
            -webkit-box-shadow: 0 0 50px 0px rgba(50, 160, 50, 0.2);
        }

        100% {
            -webkit-box-shadow: 0 0 50px 50px rgba(50, 160, 50, 0.9);
        }
    }

    @keyframes glow {
        0% {
            box-shadow: 0 0 50px rgba(50, 160, 50, 0.9);
        }

        50% {
            box-shadow: 0 0 50px rgba(50, 160, 50, 0.2);
        }

        100% {
            box-shadow: 0 0 50px rgba(50, 160, 50, 0.9);
        }
    }

    .custom-shape {
        width: 100%;
        height: auto;
        padding: 50px;
        display: flex;
        justify-content: space-around;
        align-items: center;
    }

    .egg {
        display: block;
        width: 130px;
        height: 175px;
        background-color: #32557f;
        border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
    }

    .hidden {
        display: none !important;
    }
</style>


<div class="party-blocks">
    <div class="main-search">
        <form>
            <input type="text" placeholder="search code here...." id="searchByGRN" />
            <button class="icon search-button" id="searchByGRNBtn">
                <i class="fa fa-search"></i>
            </button>

        </form>
    </div>
    <div class="name">
        <span><b>Party Name: </b> ABC LTD</span>
        <span><b>Party Code: </b> PR00001245</span>
        <span><b>Party Code: </b> PR00001245</span>
        <span><b>Party Code: </b> PR00001245</span>
    </div>
</div>
<div class="container family-tree">
    <ul id="familyTree"></ul>
</div>

<script>
    $(document).ready(function() {
        // Your JSON data
        var jsonData = {
            name: 'Salim',
            price: '1549',
            date: '15-12-2023',
            grn: 'GRN000100',
            rete: '20',
            status: 'active',
            children: [{
                    name: 'Child 2',
                    price: '1549',
                    date: '15-12-2023',
                    grn: 'GRN000110',
                    rete: '20',
                    status: 'active',
                    parent: 'GRN000100',
                    children: [{
                            name: 'Grandchild 1',
                            price: '1549',
                            date: '15-12-2023',
                            grn: 'GRN000111',
                            rete: '20',
                            status: 'inactive',
                            parent: 'GRN000110',
                        },
                        {
                            name: 'Grandchild 2',
                            price: '1549',
                            date: '15-12-2023',
                            grn: 'GRN000112',
                            rete: '20',
                            status: 'active',
                            parent: 'GRN000110',
                        },
                    ],
                },
                {
                    name: 'Child 3',
                    price: '1549',
                    date: '15-12-2023',
                    grn: 'GRN000120',
                    rete: '20',
                    status: 'active',
                    parent: 'GRN000100',
                    children: [{
                            name: 'Grandchild 1',
                            price: '1549',
                            date: '15-12-2023',
                            grn: 'GRN000121',
                            rete: '20',
                            status: 'active',
                            parent: 'GRN000120',
                        },
                        {
                            name: 'Grandchild 2',
                            price: '1549',
                            date: '15-12-2023',
                            grn: 'GRN000122',
                            rete: '20',
                            status: 'inactive',
                            parent: 'GRN000120',
                        },
                    ],
                },
            ],
        };

        // Function to create tree nodes
        function createNode(node, parentCount, hiddenClass) {

            var li = $("<li></li>");
            var childCount = (node.children && node.children.length > 0) ? node.children.length : 0;
            if (node.parent) {
                parentCount += 1;
            }

            // Add 'hiddenClass' to the card class
            var cardClass = node.status === "active" ? "parent-card" : "onprocess " + hiddenClass;

            li.append(`  
                    <label class="parent-label ${hiddenClass}">
                        <div class="card ${cardClass}">
                        ${parentCount === 0  ? `` : `<div class="plus">
                            <button class="bdr-ripple-ani-btn" data-grnparent="${node.grn}">${parentCount}</button>
                        </div>`}
                        <div class="minus">
                            <button class="bdr-ripple-ani-btn">${childCount}</button>
                        </div>
                            <div class="card-header py-3">
                                <h2 class="docNo text-xs">${node.grn}</h2>
                                <p class="docPostDate text-xs">${node.date}</p>
                            </div>
                            <div class="card-body">
                                <div class="vertical-text-section">
                                <a href="#">
                                    <span class="docNo">
                                        GRN000012
                                    </span>
                                </a>
                                </div>
                                <div class="hori-text-section">
                                <div class="amount-sec">
                                <a href="#">
                                    <span class="font-bold amount">$ ${node.price}</span>
                                </a>
                                </div>
                                <div class="body-block">
                                    <div class="details">
                                        <p class="mb-1"><span class="font-bold">Qty</span> : 20/200</p>
                                        <p class="mb-1"><span class="font-bold">Rate</span> : $ ${node.rete}</p>
                                    </div>
                                    <div class="created-date">
                                        <i class="fa fa-user"></i>
                                        <div class="date-detail">
                                            <p class="font-bold mb-1">${node.name}</p>
                                            <p class="mb-1">${node.date}</p>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </label>
                `);

            if (node.children && node.children.length > 0) {
                var ul = $("<ul class='hidden'></ul>");
                var minusButton = li.find(".minus button");
                var plusButton = li.find(".plus button");

                function findParentDetails(jsonData, childGRN) {
                    // Iterate through the jsonData to find the parent with matching child GRN
                    for (var i = 0; i < jsonData.children.length; i++) {
                        var parent = jsonData.children[i];
                        var foundChild = findChildByGRN(parent, childGRN);

                        if (foundChild) {
                            // Parent found with matching child GRN
                            return parent;
                        }
                    }

                    return null; // If no parent found
                }

                function findChildByGRN(node, childGRN) {
                    if (node.grn === childGRN) {
                        // Child found
                        return node;
                    }

                    if (node.children && node.children.length > 0) {
                        for (var i = 0; i < node.children.length; i++) {
                            var result = findChildByGRN(node.children[i], childGRN);
                            if (result) {
                                return result;
                            }
                        }
                    }

                    return null;
                }

                // Toggle visibility of the parent div when clicking the plus button
                plusButton.on("click", function(e) {
                    e.stopPropagation();

                    let getGrnParent = $(this).data('grnparent');
                    let parentResult = findParentDetails(jsonData, getGrnParent);
                    findChildByGRN(jsonData, getGrnParent);

                });

                // Toggle visibility of children when clicking the minus button
                minusButton.on("click", function(e) {
                    e.stopPropagation();
                    ul.toggleClass('hidden');
                });

                $.each(node.children, function(index, child) {
                    ul.append(createNode(child, parentCount, hiddenClass, node)); // Pass parent details
                });
                li.append(ul);
            }

            return li;
        }

        // Function to search for a node by GRN
        function searchByGRN(node, grn) {
            if (node.grn === grn) {
                return node;
            }

            if (node.children && node.children.length > 0) {
                for (var i = 0; i < node.children.length; i++) {
                    var result = searchByGRN(node.children[i], grn);
                    if (result) {
                        return result;
                    }
                }
            }

            return null;
        }

        // Create the family tree with all cards initially hidden
        var familyTree = $("#familyTree");
        familyTree.append(createNode(jsonData, 0, "hidden", null));

        $("#searchByGRNBtn").on("click", function(event) {
            event.preventDefault();

            // Get the GRN value from the input field
            var searchedGRN = $("#searchByGRN").val();
            // Search for the node
            var resultNode = searchByGRN(jsonData, searchedGRN);

            // Clear existing family tree content
            $("#familyTree").empty();

            // If the node is found, append only the resultNode to the family tree
            if (resultNode) {
                var resultTree = $("<ul></ul>");
                resultTree.append(createNode(resultNode, 0, ""));
                $("#familyTree").append(resultTree);
            } else {
                console.log("Node not found for GRN:", searchedGRN);
            }
        });

    });
</script>



<?php

require_once("../common/footer.php");

?>