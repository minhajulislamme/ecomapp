<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BrandController extends Controller
{
    public function AllBrands()
    {   
        $brands = Brand::latest()->get();
        return view('backend.brand.all_brands', compact('brands'));
    }//end of all brands

    public function AddBrand()
    {
        return view('backend.brand.add_brand');
    }//end of add brand

    public function StoreBrand(Request $request)
    {
        $dir = base_path('public/upload/brand_images');
        if(!file_exists($dir)){
            mkdir($dir, 0777, true);
        }

        $request->validate([
            'brand_name' => 'required',
            'brand_image' => 'required',
        ]);

        $manager = new ImageManager(new Driver());
        $name_gen = hexdec(uniqid()).'.'.$request->brand_image->getClientOriginalExtension();
        $image = $manager->read($request->file('brand_image'))->resize(300, 200)->toJpeg(80);
        $image->save(base_path('public/upload/brand_images/'.$name_gen));
        $save_url = 'upload/brand_images/'.$name_gen;
        Brand::create([
            'brand_name' => $request->brand_name,
            'brand_slug' => strtolower(str_replace(' ', '-',$request->brand_name)),
            'brand_image' => $save_url,
        ]);
        $notification = array(
            'message' => 'Brand Updated with image Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.brands')->with($notification);
    }//end of store brand

    public function EditBrand($id)
    {
        $brand = Brand::find($id);
        return view('backend.brand.edit_brand', compact('brand'));
    }//end of edit brand

    public function UpdateBrand(Request $request, )
    {
       $dir = base_path('public/upload/brand_images');
        if(!file_exists($dir)){
            mkdir($dir, 0777, true);
        }
        
        $brand_id = $request->id;
        $old_img = $request->old_image;
       
        if($request->file('brand_image')){
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$request->brand_image->getClientOriginalExtension();
            $image = $manager->read($request->file('brand_image'))->resize(300, 200)->toJpeg(80);
            $image->save(base_path('public/upload/brand_images/'.$name_gen));
            $save_url = 'upload/brand_images/'.$name_gen;
            if(file_exists($old_img)){
                unlink($old_img);
            }
            Brand::findOrFail($brand_id)->update([
                'brand_name' => $request->brand_name,
                'brand_slug' => strtolower(str_replace(' ', '-',$request->brand_name)),
                'brand_image' => $save_url,
            ]);
            $notification = array(
                'message' => 'Brand Updated with image Successfully',
                'alert-type' => 'success'
            );
            return redirect()->route('all.brands')->with($notification);
        }else{
            Brand::findOrFail($brand_id)->update([
                'brand_name' => $request->brand_name,
                'brand_slug' => strtolower(str_replace(' ', '-',$request->brand_name)),
            ]);
        }
        $notification = array(
            'message' => 'Brand Updated with image Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.brands')->with($notification);
    }//end of update brand

    public function DeleteBrand($id)
    {
        $brand = Brand::find($id);
        if(file_exists($brand->brand_image)){
            unlink($brand->brand_image);
        }
        $brand->delete();
        $notification = array(
            'message' => 'Brand Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.brands')->with($notification);
    }//end of delete brand
}




