# Sample Products Module

This module is responsible to render additional button to get sample of products for which samples are set up.
`Sample Products` should be `Simple Products` with zero prices and SKU same as the original product having a suffix `_sample`
E.g: If product SKU is `MS1000` it's sample product SKU should be `MS1000_sample`
Also sample should be added in related products section of main product.

### BACK-END

### `\WebVision\SampleProduct\Mapper\CustomerDataMapper`
This mapper class is responsible to get customer billing details submitted by customer from session if user is logged-in
and save it for that customer. If user is guest than billing details should be saved in Cookies.

### `\WebVision\SampleProduct\Validator\CustomerSampleProductOrderValidator`
This validator class validates if user is allowed to get a sample product. If logged-in than 10 sample orders are allowed.
If customer is guest than only 3 samples per session is allowed.

### `\WebVision\SampleProduct\Service\CreateSampleProductOrderService`
This service is used to create an order with sample product requested by customer.

### FRONT-END
For Product Description page `catalog_product_view` is extended with template `view/frontend/templates/product/view/sample-product.phtml`
For Product Listing page `catalog_category_view` is extended with template `view/frontend/templates/product/sample-product.phtml`
Both of the above templates are responsible to display `Get Sample` button and open a pop-up for billing details on click
with ViewModel `\WebVision\SampleProduct\ViewModel\SampleProductViewModel` to check if product have any sample product

`view/frontend/templates/billing-details.phtml` is the billing details form 
with ViewModel `\WebVision\SampleProduct\ViewModel\BillingDetailsViewModel`
which auto-populates the form if user trying to order a sample again.

