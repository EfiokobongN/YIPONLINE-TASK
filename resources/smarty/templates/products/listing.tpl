{* resources/smarty/templates/products/listing.tpl *}
{* Smarty version of the product listing page — bonus integration *}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products — {$appName}</title>
</head>
<body>

<h1>Products (Smarty Template)</h1>

{if $products}
    <div class="product-grid">
        {foreach $products as $product}
            <div class="product-card">
                <h3><a href="/products/{$product->slug}">{$product->name|escape}</a></h3>
                <p class="price">
                    &#8358;{$product->price|number_format:2}
                    {if $product->compare_price}
                        <s>&#8358;{$product->compare_price|number_format:2}</s>
                    {/if}
                </p>
                <p class="category">{$product->category|capitalize}</p>

                {if $product->isInStock()}
                    <form method="POST" action="/cart/add">
                        <input type="hidden" name="_token" value="{csrf_token()}">
                        <input type="hidden" name="product_id" value="{$product->id}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit">Add to Cart</button>
                    </form>
                {else}
                    <span class="out-of-stock">Out of Stock</span>
                {/if}
            </div>
        {/foreach}
    </div>
{else}
    <p>No products found.</p>
{/if}

{* Pagination *}
{if $products->hasPages()}
    <nav>
        {$products->links()|nofilter}
    </nav>
{/if}

</body>
</html>
