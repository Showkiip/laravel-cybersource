<!DOCTYPE html>
<html>

<head>
    <title>Secure Acceptance - Payment Form</title>
    <link rel="stylesheet" type="text/css" href="{{ url('cybersource/assets/css/payment.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .row {
            display: flex;
            margin-bottom: 10px;
        }

        .label {
            width: 30%;
        }

        .input {
            width: 70%;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        textarea {
            height: 100px;
        }

        .credit-card {
            width: 350px;
            height: 200px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #f5f5f5;
            display: flex;
            perspective: 1000px;
        }

        .card-front,
        .card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            transition: transform 0.5s ease-in-out;
        }

        .credit-card:hover .card-front {
            transform: rotateY(180deg);
        }

        .card-front {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
        }

        .chip {
            width: 40px;
            height: 20px;
            border-radius: 2px;
            background-color: #000;
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .card-number {
            font-size: 16px;
            font-weight: bold;
            color: #000;
            margin-top: 30px;
        }

        .card-holder-name {
            font-size: 14px;
            color: #333;
            margin-top: 10px;
        }

        .valid-thru {
            font-size: 10px;
            color: #999;
            position: absolute;
            bottom: 10px;
            left: 10px;
        }

        .expiration-date {
            font-size: 12px;
            font-weight: bold;
            color: #000;
            position: absolute;
            bottom: 10px;
            right: 10px;
        }

        .card-back {
            background-color: #ccc;
            transform: rotateY(180deg);
        }

        .magnetic-stripe {
            width: 100%;
            height: 5px;
            background-color: #000;
            opacity: 0.2;
            position: absolute;
            bottom: 10px;
        }

        .cvv {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            position: absolute;
            right: 10px;
            bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">


        <img src="{{ url('cybersource/assets/img/logo-cybersource.png') }}" style="padding-bottom: 10px;" />

        <div class="container">
            <h2>Payment Information</h2>
            <form action="{{ route('payment') }}" method="post">
                @csrf
                <div class="row">
                    <div class="label">
                        <label for="payee">Payee Name:</label>
                    </div>
                    <div class="input">
                        <input type="text" id="payee" name="payee" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="label">
                        <label for="amount">Amount to be Credited (USD):</label>
                    </div>
                    <div class="input">
                        <input type="text" id="amount" name="amount">
                    </div>
                </div>
                <div class="row">
                    <div class="label">
                        <label for="total">Total Payment (USD):</label>
                    </div>
                    <div class="input">
                        <input type="text" id="total_amount" name="total_amount" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="label">
                        <label for="credit_to">To Be Credited To:</label>
                    </div>
                    <div class="input">
                        <input type="text" id="credit_to" name="credit_to" placeholder="Enter Name (Optional)">
                    </div>
                </div>
                <div class="row">
                    <div class="label">
                        <label for="payment_type">Payment Type:</label>
                    </div>
                    <div class="input">
                        <select id="payment_type" name="payment_type">
                            <option value="payment">Payment</option>
                            <option value="sponsership">Sponsership</option>
                            <option value="donation">Donation</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="label">
                        <label for="description">Description:</label>
                    </div>
                    <div class="input">
                        <textarea id="description" name="description" placeholder="Enter Description (Optional)"></textarea>
                    </div>
                </div>

                <span>Card Number:</span> <input type="text" id="card_number" name="card_number" class="form-control"
                    onchange="validateCardNumber()"> <br />
                <span>Expiration Month:</span> <input type="text" id="expiration_month" name="expiration_month"
                    class="form-control" maxlength="2"> <br />
                <span>Expiration Year:</span> <input type="text" id="expiration_year" name="expiration_year"
                    class="form-control" maxlength="4"> <br />
                <span>CVV:</span> <input type="text" id="cvv" name="cvv" class="form-control"
                    maxlength="3"> <br />

                <div class="button-container text-center">
                    <input type="submit" id="btn_submit" value="Submit" />
                </div>
            </form>
            {{-- <form action="{{ route('test') }}" method="post">
            @csrf
            <fieldset>
                <legend>Payment Details</legend>
                <span>full Name:</span> <input type="text" name="full_name" class="form-control"><br />
                <span>Amount:</span> <input type="text" name="amount" class="form-control"><br />
                <span>To be credited to:</span> <input type="text" name="credited_to" class="form-control"><br />
                <span>Payment Type:</span> <select name="payment_typw" class="form-control">
                    <option value="payment">Payment</option>
                    <option value="sponsership">Sponsership</option>
                    <option value="donation">Donation</option>
                </select><br />
                <span>Description:</span>
                <textarea type="text" name="description" class="form-control"> </textarea><br />
            </fieldset>
            <p>
            <fieldset>
                <legend>Contact</legend>
                So we can contact you if needed.<br />
                <span>Email *:</span> <input type="text" name="email" class="form-control"><br />
                <span>Phone *:</span> <input type="text" name="phone" class="form-control"><br />

            </fieldset>


            <!-- MDD END -->
            <input type="submit" id="btn_submit" value="Submit" />
        </form> --}}
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

        <script>
            $(document).ready(function() {
                $("#amount").on("keyup", function() {
                    // Get the amount entered by the user
                    var amount = parseFloat($(this).val());
                    var totalAmount = (amount * 25) / 100;
                    $("#total_amount").val(totalAmount.toFixed(2)); // Format total amount to 2 decimal places
                });
            });

            $(document).ready(function() {
                $("#card_number").on("change", function() {
                    validateCardNumber();
                });
            });

            function validateCardNumber() {
                var cardNumber = $("#card_number").val();
                // Regular expression for Visa card numbers (4 digits starting with 4)
                var visaRegex = /^4[0-9]{12}(?:[0-9]{3})?$/;
                // Check if the card number matches the Visa regex
                if (!visaRegex.test(cardNumber)) {
                    $("#card-error").text("Invalid Card Number (Must be a Visa card)");
                    return false;
                } else {
                    $("#card-error").text("");
                }
            }
        </script>
</body>

</html>
