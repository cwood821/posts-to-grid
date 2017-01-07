# Posts-to-Grid
Posts-to-Grid provides provides access to the WordPress [get_posts()](https://developer.wordpress.org/reference/functions/get_posts/) function via a shortcode. Posts are outputted as a grid of featured images with the title as a link below.

## The Shortcode
[postgrid ...]

Besides accepting all of the [get_posts()](https://developer.wordpress.org/reference/functions/get_posts/), the shortcode also accepts the following parameters:

<table>
    <tr>
        <td>cols</td>
        <td>The number of columbs (1-4) that you want the outputted grid to have. Defaults to 3.</td>
    </tr>    
    <tr>
        <td>height</td>
        <td>The height of the featured image blocks in the grid, formatted as a CSS value (e.g. '15em', '200px'). Defaults to 15em.</td>
    </tr>
</table>

## Example

[postgrid cols="3" posts_per_page="3" height="15em"]


# Future Development
-Refine responsive image integration
-Add option to choose the featured image size (instead of defaulting to large)
-Add a WordPress Admin Settings page
