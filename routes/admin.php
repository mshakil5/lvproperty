<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\FAQController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\CompanyDetailsController;
use App\Http\Controllers\Admin\SubSubCategoryController;
use App\Http\Controllers\Admin\LandlordController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\ComplianceTypeController;
use App\Http\Controllers\Admin\PropertyComplianceController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ExpenseTransactionController;

Route::group(['prefix' =>'admin/', 'middleware' => ['auth', 'is_admin']], function(){
    Route::get('/dashboard', [HomeController::class, 'adminHome'])->name('admin.dashboard');
    // User
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/user-update', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.delete');
    Route::post('/user-status', [UserController::class, 'toggleStatus'])->name('user.toggleStatus');

    // Slider
    Route::get('/slider', [SliderController::class, 'getSlider'])->name('allslider');
    Route::post('/slider', [SliderController::class, 'sliderStore']);
    Route::get('/slider/{id}/edit', [SliderController::class, 'sliderEdit']);
    Route::post('/slider-update', [SliderController::class, 'sliderUpdate']);
    Route::delete('/slider/{id}', [SliderController::class, 'sliderDelete'])->name('slider.delete');
    Route::post('/slider-status', [SliderController::class, 'toggleStatus']);
    Route::post('/sliders/update-order', [SliderController::class, 'updateOrder'])->name('sliders.updateOrder');

    // Contact
    Route::get('/contacts', [ContactController::class,'index'])->name('contacts.index');
    Route::get('/contacts/{id}', [ContactController::class,'show'])->name('contacts.show');
    Route::delete('/contacts/{id}/delete', [ContactController::class,'destroy'])->name('contacts.delete');
    Route::post('/contacts/toggle-status', [ContactController::class,'toggleStatus'])->name('contacts.toggleStatus');

    // FAQ
    Route::get('/faq', [FAQController::class, 'index'])->name('faq.index');
    Route::post('/faq', [FAQController::class, 'store'])->name('faq.store');
    Route::get('/faq/{id}/edit', [FAQController::class, 'edit'])->name('faq.edit');
    Route::post('/faq-update', [FAQController::class, 'update'])->name('faq.update');
    Route::delete('/faq/{id}', [FAQController::class, 'destroy'])->name('faq.delete');

    Route::get('/company-details', [CompanyDetailsController::class, 'index'])->name('admin.companyDetails');
    Route::post('/company-details', [CompanyDetailsController::class, 'update'])->name('admin.companyDetails');

    Route::get('/company/seo-meta', [CompanyDetailsController::class, 'seoMeta'])->name('admin.company.seo-meta');
    Route::post('/company/seo-meta/update', [CompanyDetailsController::class, 'seoMetaUpdate'])->name('admin.company.seo-meta.update');

    Route::get('/about-us', [CompanyDetailsController::class, 'aboutUs'])->name('admin.aboutUs');
    Route::post('/about-us', [CompanyDetailsController::class, 'aboutUsUpdate'])->name('admin.aboutUs');

    Route::get('/privacy-policy', [CompanyDetailsController::class, 'privacyPolicy'])->name('admin.privacy-policy');
    Route::post('/privacy-policy', [CompanyDetailsController::class, 'privacyPolicyUpdate'])->name('admin.privacy-policy');

    Route::get('/terms-and-conditions', [CompanyDetailsController::class, 'termsAndConditions'])->name('admin.terms-and-conditions');
    Route::post('/terms-and-conditions', [CompanyDetailsController::class, 'termsAndConditionsUpdate'])->name('admin.terms-and-conditions');
    
    Route::get('/mail-body', [CompanyDetailsController::class, 'mailBody'])->name('admin.mail-body');
    Route::post('/mail-body', [CompanyDetailsController::class, 'mailBodyUpdate'])->name('admin.mail-body');

    Route::get('/home-footer', [CompanyDetailsController::class, 'homeFooter'])->name('admin.home-footer');
    Route::post('/home-footer', [CompanyDetailsController::class, 'homeFooterUpdate'])->name('admin.home-footer');

    Route::get('/copyright', [CompanyDetailsController::class, 'copyright'])->name('admin.copyright');
    Route::post('/copyright', [CompanyDetailsController::class, 'copyrightUpdate'])->name('admin.copyright');

    Route::get('/master', [MasterController::class, 'index'])->name('master.index');
    Route::post('/master', [MasterController::class, 'store'])->name('master.store');
    Route::get('/master/{id}/edit', [MasterController::class, 'edit'])->name('master.edit');
    Route::post('/master-update', [MasterController::class, 'update'])->name('master.update');
    Route::delete('/master/{id}', [MasterController::class, 'destroy'])->name('master.delete');

    Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
    Route::post('/sections/update-order', [SectionController::class, 'updateOrder'])->name('sections.updateOrder');
    Route::post('/sections/toggle-status', [SectionController::class, 'toggleStatus'])->name('sections.toggleStatus');

    // Category crud
    Route::get('/category', [CategoryController::class, 'index'])->name('allcategory');
    Route::get('/parent-categories', [CategoryController::class, 'parentCategories'])->name('parent.categories');
    Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/category/{id}/edit', [CategoryController::class, 'edit']);
    Route::post('/category-update', [CategoryController::class, 'update']);
    Route::delete('/category/{id}', [CategoryController::class, 'delete'])->name('category.delete');
    Route::post('/category-status', [CategoryController::class, 'toggleStatus']);

    // Landlord crud
    Route::get('/landlord', [LandlordController::class, 'index'])->name('alllandlord');
    Route::post('/landlord', [LandlordController::class, 'store'])->name('landlord.store');
    Route::get('/landlord/{id}/edit', [LandlordController::class, 'edit']);
    Route::post('/landlord-update', [LandlordController::class, 'update']);
    Route::delete('/landlord/{id}', [LandlordController::class, 'delete'])->name('landlord.delete');
    Route::post('/landlord-status', [LandlordController::class, 'toggleStatus']);

    // Property crud
    Route::get('/property', [PropertyController::class, 'index'])->name('allproperty');
    Route::post('/property', [PropertyController::class, 'store'])->name('property.store');
    Route::get('/property/{id}/edit', [PropertyController::class, 'edit']);
    Route::post('/property-update', [PropertyController::class, 'update']);
    Route::delete('/property/{id}', [PropertyController::class, 'delete'])->name('property.delete');

    // Tenant crud
    Route::get('/tenant', [TenantController::class, 'index'])->name('alltenant');
    Route::post('/tenant', [TenantController::class, 'store'])->name('tenant.store');
    Route::get('/tenant/{id}/edit', [TenantController::class, 'edit']);
    Route::post('/tenant-update', [TenantController::class, 'update']);
    Route::delete('/tenant/{id}', [TenantController::class, 'delete'])->name('tenant.delete');
    Route::post('/tenant-status', [TenantController::class, 'toggleStatus']);
    Route::post('/tenant-renew', [TenantController::class, 'renewTenancy'])->name('tenant.renew');
    Route::post('/tenant-terminate', [TenantController::class, 'terminateTenancy']);

    Route::get('/tenant/{id}/transactions', [TenantController::class, 'getTenantTransactions'])->name('tenant.transactions');
    Route::post('/tenant/receive-payment', [TenantController::class, 'receivePayment'])->name('tenant.receive-payment');

    Route::get('/compliance-type', [ComplianceTypeController::class, 'index'])->name('allcompliance-type');
    Route::post('/compliance-type', [ComplianceTypeController::class, 'store'])->name('compliance-type.store');
    Route::get('/compliance-type/{id}/edit', [ComplianceTypeController::class, 'edit'])->name('compliance-type.edit');
    Route::post('/compliance-type-update', [ComplianceTypeController::class, 'update'])->name('compliance-type.update');
    Route::delete('/compliance-type/{id}', [ComplianceTypeController::class, 'delete'])->name('compliance-type.delete');
    Route::post('/compliance-type-status', [ComplianceTypeController::class, 'toggleStatus'])->name('compliance-type-status');

    // Property Compliance crud
    Route::get('/property-compliance', [PropertyComplianceController::class, 'index'])->name('allproperty-compliance');
    Route::post('/property-compliance', [PropertyComplianceController::class, 'store'])->name('property-compliance.store');
    Route::get('/property-compliance/{id}/edit', [PropertyComplianceController::class, 'edit']);
    Route::post('/property-compliance-update', [PropertyComplianceController::class, 'update']);
    Route::delete('/property-compliance/{id}', [PropertyComplianceController::class, 'delete'])->name('property-compliance.delete');
    Route::post('/property-compliance/receive-payment', [PropertyComplianceController::class, 'receivePayment'])->name('property-compliance.receive-payment');

    // Expense crud
    Route::get('/expense-category', [ExpenseController::class, 'index'])->name('allexpense');
    Route::post('/expense-category', [ExpenseController::class, 'store'])->name('expense.store');
    Route::get('/expense-category/{id}/edit', [ExpenseController::class, 'edit']);
    Route::post('/expense-category-update', [ExpenseController::class, 'update']);
    Route::delete('/expense-category/{id}', [ExpenseController::class, 'delete'])->name('expense.delete');
    Route::post('/expense-category-status', [ExpenseController::class, 'toggleStatus']);

    Route::get('/expenses', [ExpenseTransactionController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseTransactionController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{id}/edit', [ExpenseTransactionController::class, 'edit'])->name('expenses.edit');
    Route::post('/expenses-update', [ExpenseTransactionController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{id}', [ExpenseTransactionController::class, 'delete'])->name('expenses.delete');
    Route::post('/expenses-status', [ExpenseTransactionController::class, 'toggleStatus'])->name('expenses.status');

});