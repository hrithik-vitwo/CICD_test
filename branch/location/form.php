<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Form with API</title>
    <style>
        /* Basic reset and body setup */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Form container */
        .form-container {
            background-color: white;
            width: 100%;
            max-width: 450px;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* Form heading */
        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        /* General form styling */
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Form fields */
        .form-field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-size: 14px;
            color: #555;
            text-align: left;
        }

        input, select, button {
            font-size: 14px;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            transition: border-color 0.3s ease;
        }

        input[type="text"], input[type="email"], input[type="date"], select {
            background-color: #fafafa;
        }

        /* Input focus styles */
        input:focus, select:focus {
            outline: none;
            border-color: #4CAF50;
        }

        /* Required fields */
        label::after {
            content: '*';
            color: red;
            margin-left: 5px;
        }

        /* Button styling */
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #45a049;
        }

        /* Error message styling */
        .error {
            color: red;
            font-size: 12px;
            margin-top: -10px;
        }

        /* Conditional fields */
        .form-field.hidden {
            display: none;
        }

        /* Responsive styling */
        @media (max-width: 480px) {
            .form-container {
                padding: 20px;
            }

            form {
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Dynamic Form</h2>
        <form id="dynamicForm" method="POST" action="submit.php">
            <div id="formFields"></div> <!-- Dynamic Fields will be inserted here -->

            <!-- Customers Dropdown -->
            <div class="form-field">
                <label for="customers">Customers</label>
                <select id="customers" name="customers" required>
                    <option value="">Select a Customer</option>
                    <!-- Customers will be dynamically populated here -->
                </select>
            </div>

            <!-- Customer Details Section -->
            <div id="customerDetails" class="form-field hidden">
                <!-- Customer details will be displayed here -->
            </div>

            <button type="submit">Submit</button>
        </form>
    </div>

    <script>
        // Fetch dynamic form fields from the API
        async function fetchFormFields() {
            try {
                const apiUrl = 'formApi.php'; // Replace with your form fields API
                const response = await fetch(apiUrl);
                const data = await response.json();

                if (data && data.fields) {
                    renderFormFields(data.fields);
                }
            } catch (error) {
                console.error('Error fetching form fields:', error);
            }
        }

        // Fetch customers data from the API
        async function fetchCustomers() {
            try {
                const customerApiUrl = 'getcustomers.php'; // Replace with your customer data API
          
                const response = await fetch(customerApiUrl);

                console.log(response);
                const data = await response.json();

                if (data && data.customers) {
                    populateCustomerDropdown(data.customers);
                }
            } catch (error) {
                console.error('Error fetching customers:', error);
            }
        }

        // Populate Customers dropdown
        function populateCustomerDropdown(customers) {
            const customersSelect = document.getElementById('customers');
            customers.forEach(customer => {
                const option = document.createElement('option');
                option.value = customer.customer_id;
                option.textContent = customer.trade_name;
                customersSelect.appendChild(option);
            });

            // Listen for customer selection
            customersSelect.addEventListener('change', function () {
                const selectedCustomerId = customersSelect.value;
                if (selectedCustomerId) {
                    fetchCustomerDetails(selectedCustomerId);
                } else {
                    document.getElementById('customerDetails').classList.add('hidden');
                }
            });
        }

        // Fetch selected customer details
        async function fetchCustomerDetails(customerId) {
            try {
                const detailsApiUrl = `https://your-api.com/get-customer-details/${customerId}`; // Replace with your customer details API
                const response = await fetch(detailsApiUrl);
                const data = await response.json();

                if (data && data.customer) {
                    displayCustomerDetails(data.customer);
                }
            } catch (error) {
                console.error('Error fetching customer details:', error);
            }
        }

        // Display customer details in the form
        function displayCustomerDetails(customer) {
            const customerDetailsContainer = document.getElementById('customerDetails');
            customerDetailsContainer.classList.remove('hidden');
            customerDetailsContainer.innerHTML = `
                <label for="customerDetails">Customer Details</label>
                <div><strong>Name:</strong> ${customer.trade_name}</div>
                <!-- You can add more customer details here -->
            `;
        }

        // Render dynamic fields based on the API response
        function renderFormFields(fields) {
            const formContainer = document.getElementById('formFields');
            fields.forEach(field => {
                let fieldElement;

                // Handle different types of fields (text, email, select, date, etc.)
                switch (field.type) {
                    case 'text':
                    case 'email':
                        fieldElement = createTextField(field);
                        break;
                    case 'select':
                        fieldElement = createSelectField(field);
                        break;
                    case 'date':
                        fieldElement = createDateField(field);
                        break;
                    case 'textarea':
                        fieldElement = createTextareaField(field);
                        break;
                    default:
                        return;
                }

                // Append the field element to the form
                formContainer.appendChild(fieldElement);
            });
        }

        // Create a text input field
        function createTextField(field) {
            const div = document.createElement('div');
            div.classList.add('form-field');
            div.innerHTML = `
                <label for="${field.name}">${field.label} ${field.required ? '*' : ''}</label>
                <input type="${field.type}" id="${field.name}" name="${field.name}" ${field.required ? 'required' : ''}>
            `;
            return div;
        }

        // Create a select input field
        function createSelectField(field) {
            const div = document.createElement('div');
            div.classList.add('form-field');
            let optionsHtml = field.options.map(option => `<option value="${option}">${option}</option>`).join('');
            div.innerHTML = `
                <label for="${field.name}">${field.label} ${field.required ? '*' : ''}</label>
                <select id="${field.name}" name="${field.name}" ${field.required ? 'required' : ''}>
                    ${optionsHtml}
                </select>
            `;
            return div;
        }

        // Create a date input field
        function createDateField(field) {
            const div = document.createElement('div');
            div.classList.add('form-field');
            div.innerHTML = `
                <label for="${field.name}">${field.label} ${field.required ? '*' : ''}</label>
                <input type="${field.type}" id="${field.name}" name="${field.name}" ${field.required ? 'required' : ''}>
            `;
            return div;
        }

        // Create a textarea input field
        function createTextareaField(field) {
            const div = document.createElement('div');
            div.classList.add('form-field');
            div.innerHTML = `
                <label for="${field.name}">${field.label} ${field.required ? '*' : ''}</label>
                <textarea id="${field.name}" name="${field.name}" rows="4" ${field.required ? 'required' : ''}></textarea>
            `;
            return div;
        }

        // Fetch form fields and customers data on page load
        window.onload = function () {
            fetchFormFields();
            fetchCustomers();
        };
    </script>
</body>
</html>
