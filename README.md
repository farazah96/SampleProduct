## Setup

Create a Simple Product for a Configurable product with SKU containing `_sample` and add that sample product in related products of configurable one.

Add `WebVision` module in your `app/code/` folder
Add `WebVision_SampleProduct` to `app/design/frontend/luma/` folder
Run `setup:upgrade` and `setup:deploy:content`
Go to Categories page add look for the product for which you added a sample. It will contain a button `Get Sample`
A pop-up will be opened once you click the button. 
Fill `billing details` and submit the form.
You order will be placed for a `Sample Product`.
