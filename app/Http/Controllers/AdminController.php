<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function brands()
    {
        $brands = Brand::orderBy('id', 'DESC')->paginate(10);
        return view('admin.brands', compact('brands'));
    }

    public function brandAdd()
    {
        return view('admin.brand-add');
    }

    public function brandStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:jpg,jpeg,png|max:2048',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extention;

        $this->GenerateBrandThumbnailsImage($image, $file_name);

        $brand->image = $file_name;
        $brand->save();

        return redirect()->route('admin.brands')->with('status', 'Brand has  been added successfully');
    }

    public function brandEdit($id)
    {
        $brand = Brand::find($id);
        return view('admin.brands-edit', compact('brand'));
    }

    public function brandUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$request->id,
            'image' => 'mimes:jpg,jpeg,png|max:2048',
        ]);

        $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->slug);
        if($request->hasFile('image'))
        {
            if(File::exists(public_path('uploads/brands'.'/'.$brand->image)))
            {
                File::delete(public_path('uploads/brands'.'/'.$brand->image));
            }

            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extention;

            $this->GenerateBrandThumbnailsImage($image, $file_name);

            $brand->image = $file_name;
        }

        $brand->save();

        return redirect()->route('admin.brands')->with('status', 'Brand has been updated successfully');
    }

    public function GenerateBrandThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->path());
        $img->cover(124,124,"top");
        $img->resize(124, 124,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function brandDelete($id)
    {
        $brand = Brand::find($id);

        if (File::exists(public_path('uploads/brands'.'/'.$brand->image)))
        {
            File::delete(public_path('uploads/brands'.'/'.$brand->image));
        }

        $brand->delete();
        return redirect()->route('admin.brands')->with('status', 'Brand has been deleted successfully');
    }

    public function categories()
    {
        $categories =  Category::orderBy('id', 'DESC')->paginate(10);
        return view('admin.categories', compact('categories'));
    }

    public function categoryAdd()
    {
        return view('admin.category-add');
    }

    public function GenerateCategoryThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124,124,"top");
        $img->resize(124, 124,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function categoryStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:jpg,jpeg,png|max:2048',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extention;

        $this->GenerateCategoryThumbnailsImage($image, $file_name);

        $category->image = $file_name;
        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Category has  been added successfully');
    }

    public function categoryEdit($id)
    {
        $category = Category::find($id);
        return view('admin.category-edit', compact('category'));
    }

    public function categoryUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$request->id,
            'image' => 'mimes:jpg,jpeg,png|max:2048',
        ]);

        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->slug);
        if($request->hasFile('image'))
        {
            if(File::exists(public_path('uploads/categories'.'/'.$category->image)))
            {
                File::delete(public_path('uploads/categories'.'/'.$category->image));
            }

            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extention;

            $this->GenerateCategoryThumbnailsImage($image, $file_name);

            $category->image = $file_name;
        }

        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Category has been updated successfully');
    }

    public function categoryDelete($id)
    {
        $category = Category::find($id);
        if (File::exists(public_path('uploads/categories'.'/'.$category->image)))
        {
            File::delete(public_path('uploads/categories'.'/'.$category->image));
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'Category has been deleted successfully');
    }

    public function products()
    {
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.products', compact('products'));
    }

    public function productAdd()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view ('admin.products-add', compact('categories', 'brands'));
    }

    public function productStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if($request->file('image'))
        {
            $image = $request->file('image');
            $imageName = $current_timestamp.'.'.$image->extension();
            $this->GenerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;

        if($request->hasFile('images'))
        {
            $allowedfileExtensions = ["jpg", "jpeg", "png"];
            $files = $request->file('images');
            foreach($files as $file)
            {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedfileExtensions);

                if($gcheck)
                {
                    $gfileName = $current_timestamp."-".$counter.".".$gextension;
                    $this->GenerateProductThumbnailImage($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter++;
                }
            }
            $gallery_images = implode(",", $gallery_arr);
        }

        $product->images = $gallery_images;
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product has been added successfully');
    }

    public function GenerateProductThumbnailImage($image, $imageName)
    {
        $destinationPathThumbnail = public_path('uploads/products/thumbnails');
        $destinationPath = public_path('uploads/products');
        $img = Image::read($image->path());

        $img->cover(540,689,"top");
        $img->resize(540,689, function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);

        $img->resize(540, 689, function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPathThumbnail.'/'.$imageName);
    }

    public function productEdit($id)
    {
        $product = Product::find($id);
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();

        return view('admin.products-edit', compact('product', 'categories', 'brands'));
    }

    public function productUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,'.$request->id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'mimes:jpg,jpeg,png|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $product = Product::find($request->id);
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if($request->file('image'))
        {
            if(File::exists(public_path('uploads/products').'/'.$product->image))
            {
                File::delete(public_path('uploads/products').'/'.$product->image);
            }

            if(File::exists(public_path('uploads/products/thumbnails').'/'.$product->image))
            {
                File::delete(public_path('uploads/products/thumbnails').'/'.$product->image);
            }

            $image = $request->file('image');
            $imageName = $current_timestamp.'.'.$image->extension();
            $this->GenerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;

        if($request->hasFile('images'))
        {
            foreach(explode(',', $product->images) as $ofile)
            {
                if(File::exists(public_path('uploads/products').'/'.$ofile))
                {
                    File::delete(public_path('uploads/products').'/'.$ofile);
                }

                if(File::exists(public_path('uploads/products/thumbnails').'/'.$ofile))
                {
                    File::delete(public_path('uploads/products/thumbnails').'/'.$ofile);
                }
            }

            $allowedfileExtensions = ["jpg", "jpeg", "png"];
            $files = $request->file('images');
            foreach($files as $file)
            {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedfileExtensions);

                if($gcheck)
                {
                    $gfileName = $current_timestamp."-".$counter.".".$gextension;
                    $this->GenerateProductThumbnailImage($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter++;
                }
            }
            $gallery_images = implode(",", $gallery_arr);
            $product->images = $gallery_images;
        }

        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product has been updated successfully');
    }

    public function productDelete($id)
    {
        $product = Product::find($id);

        if (File::exists(public_path('uploads/products').'/'.$product->image))
        {
            File::delete(public_path('uploads/products').'/'.$product->image);
        }
        if (File::exists(public_path('uploads/products/thumbnails').'/'.$product->image))
        {
            File::delete(public_path('uploads/products/thumbnails').'/'.$product->image);
        }

        foreach(explode(',', $product->images) as $ofile)
        {
            if(File::exists(public_path('uploads/products').'/'.$ofile))
            {
                File::delete(public_path('uploads/products').'/'.$ofile);
            }

            if(File::exists(public_path('uploads/products/thumbnails').'/'.$ofile))
            {
                File::delete(public_path('uploads/products/thumbnails').'/'.$ofile);
            }
        }

        $product->delete();
        return redirect()->route('admin.products')->with('status', 'Product has been deleted successfully');
    }

    public function coupons()
    {
        $coupons = Coupon::orderBy('expiry_date', 'DESC')->paginate(12);
        return view('admin.coupons', compact('coupons'));
    }

    public function couponAdd()
    {
        return view('admin.coupon-add');
    }

    public function couponStore(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'expiry_date' => 'required|date',
            'cart_value' => 'required|numeric',
        ]);

        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('status', 'Coupon has been added successfully!');
    }

    public function couponEdit($id)
    {
        $coupon = Coupon::find($id);
        return view('admin.coupon-edit', compact('coupon'));
    }

    public function couponUpdate(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'expiry_date' => 'required|date',
            'cart_value' => 'required|numeric',
        ]);

        $coupon = Coupon::find($request->id);
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('status', 'Coupon has been updated successfully!');
    }
}
