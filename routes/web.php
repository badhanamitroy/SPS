<?php

/** @var \App\Core\Router $router */

// Public Core Routes
$router->get('/{lang}', 'HomeController@index', 'home');
$router->get('/{lang}/components', 'HomeController@componentsShowcase', 'components');

$router->get('/{lang}/about', 'SectionController@about', 'about');
$router->get('/{lang}/activities', 'SectionController@activities', 'activities');
$router->get('/{lang}/knowledge', 'SectionController@knowledge', 'knowledge');
$router->get('/{lang}/get-involved', 'SectionController@getInvolved', 'get-involved');
$router->get('/{lang}/transparency', 'SectionController@transparency', 'transparency');
$router->get('/{lang}/contact', 'SectionController@contact', 'contact');

// Library & E-Book Reader Routes
$router->get('/{lang}/library', 'LibraryController@index', 'library');
$router->get('/{lang}/library/book/{slug}', 'LibraryController@show', 'library.show');
$router->get('/{lang}/library/reader/{slug}', 'LibraryController@reader', 'library.reader');
$router->get('/{lang}/library/stream/{slug}', 'LibraryController@streamPdf', 'library.stream');
$router->post('/{lang}/library/request/{slug}', 'LibraryController@requestAccess', 'library.request');
$router->post('/{lang}/library/download-request/{slug}', 'LibraryController@requestDownload', 'library.download.request');
$router->get('/{lang}/library/download/{slug}', 'LibraryController@downloadFile', 'library.download.file');
$router->get('/{lang}/library/role', 'LibraryController@switchRole', 'library.role');
$router->post('/{lang}/library/role', 'LibraryController@switchRole', 'library.role.post');

// Admin Authentication Routes
$router->get('/{lang}/admin/login', 'AdminController@loginPage', 'admin.login');
$router->post('/{lang}/admin/login', 'AdminController@loginProcess', 'admin.login.process');
$router->post('/{lang}/admin/auth/google/verify', 'AdminController@googleVerify', 'admin.google.verify');
$router->get('/{lang}/admin/2fa', 'AdminController@twoFactorPage', 'admin.2fa');
$router->post('/{lang}/admin/2fa', 'AdminController@twoFactorVerify', 'admin.2fa.verify');
$router->post('/{lang}/admin/2fa/resend', 'AdminController@twoFactorResend', 'admin.2fa.resend');
$router->get('/{lang}/admin/force-password-change', 'AdminController@forcePasswordChangePage', 'admin.force_password_change');
$router->post('/{lang}/admin/force-password-change', 'AdminController@forcePasswordChangeSubmit', 'admin.force_password_change.submit');
$router->get('/{lang}/admin/logout', 'AdminController@logout', 'admin.logout');
$router->post('/{lang}/admin/logout', 'AdminController@logout', 'admin.logout.post');


// Admin Portal & RBAC Routes
$router->get('/{lang}/admin', 'AdminController@dashboard', 'admin.dashboard');
$router->get('/{lang}/admin/dashboard', 'AdminController@dashboard', 'admin.dashboard.alias');
$router->get('/{lang}/admin/roles', 'AdminController@roles', 'admin.roles');
$router->get('/{lang}/admin/users', 'AdminController@users', 'admin.users');
$router->post('/{lang}/admin/users/assign-role', 'AdminController@assignRole', 'admin.users.assign_role');
$router->post('/{lang}/admin/users/create', 'AdminController@createAdminUser', 'admin.users.create');
$router->post('/{lang}/admin/users/reset-otp', 'AdminController@resetAdminOtp', 'admin.users.reset_otp');
$router->get('/{lang}/admin/finance', 'AdminController@finance', 'admin.finance');
$router->get('/{lang}/admin/audit-logs', 'AdminController@auditLogs', 'admin.audit_logs');
$router->post('/{lang}/admin/switch-user', 'AdminController@switchUser', 'admin.switch_user');
$router->get('/{lang}/admin/profile', 'AdminController@profilePage', 'admin.profile');
$router->post('/{lang}/admin/profile', 'AdminController@updateProfile', 'admin.profile.update');
$router->get('/{lang}/admin/library', 'AdminController@library', 'admin.library');
$router->post('/{lang}/admin/library/book/{slug}/update', 'AdminController@updateLibraryBook', 'admin.library.book.update');
$router->post('/{lang}/admin/library/download-request/{id}', 'AdminController@updateDownloadRequest', 'admin.library.download_request.update');
$router->post('/{lang}/admin/request/{id}', 'AdminController@updateRequest', 'admin.request.update');

// Admin Activities Management (Strictly Super Admin & Admin)
$router->get('/{lang}/admin/activities', 'AdminController@activities', 'admin.activities');
$router->post('/{lang}/admin/activities/create', 'AdminController@createActivity', 'admin.activities.create');
$router->post('/{lang}/admin/activities/update/{id}', 'AdminController@updateActivity', 'admin.activities.update');
$router->post('/{lang}/admin/activities/delete/{id}', 'AdminController@deleteActivity', 'admin.activities.delete');

// Blog Moderation Routes (Super Admin, Admin, Literature-Admin)
$router->get('/{lang}/admin/blogs', 'AdminController@blogs', 'admin.blogs');
$router->post('/{lang}/admin/blogs/approve/{id}', 'AdminController@approveBlog', 'admin.blogs.approve');
$router->post('/{lang}/admin/blogs/reject/{id}', 'AdminController@rejectBlog', 'admin.blogs.reject');
$router->post('/{lang}/admin/blogs/delete/{id}', 'AdminController@deleteBlog', 'admin.blogs.delete');

// Homepage Sections Dynamic Configuration (Super Admin, Admin, Content Editor)
$router->get('/{lang}/admin/homepage', 'AdminController@homepageSections', 'admin.homepage');
$router->post('/{lang}/admin/homepage', 'AdminController@updateHomepageSections', 'admin.homepage.update');

// SPS Blog & Thought Journal (Blogspot clone + SPS theme)
$router->get('/{lang}/blog', 'BlogController@index', 'blog');
$router->get('/{lang}/blog/write', 'BlogController@writePage', 'blog.write');
$router->post('/{lang}/blog/write', 'BlogController@submitPost', 'blog.submit');
$router->get('/{lang}/blog/{slug}', 'BlogController@show', 'blog.show');
$router->post('/{lang}/blog/{slug}/like', 'BlogController@toggleLike', 'blog.like');
$router->post('/{lang}/blog/{slug}/comment', 'BlogController@addComment', 'blog.comment');

// SPS Membership System (Public Hub, Application, Login, 2FA, Google OAuth, Dashboard, Digital Card)
$router->get('/{lang}/membership', 'MembershipController@index', 'membership');
$router->get('/{lang}/membership/apply', 'MembershipController@applyForm', 'membership.apply');
$router->post('/{lang}/membership/apply', 'MembershipController@submitApplication', 'membership.apply.submit');
$router->get('/{lang}/membership/login', 'MembershipController@loginPage', 'membership.login');
$router->post('/{lang}/membership/login', 'MembershipController@loginProcess', 'membership.login.process');
$router->get('/{lang}/membership/2fa', 'MembershipController@twoFactorPage', 'membership.2fa');
$router->post('/{lang}/membership/2fa', 'MembershipController@twoFactorVerify', 'membership.2fa.verify');
$router->post('/{lang}/membership/2fa/resend', 'MembershipController@twoFactorResend', 'membership.2fa.resend');
$router->get('/{lang}/membership/auth/google', 'MembershipController@googleRedirect', 'membership.google.login');
$router->post('/{lang}/membership/auth/google/verify', 'MembershipController@googleVerify', 'membership.google.verify');
$router->get('/{lang}/membership/auth/google/callback', 'MembershipController@googleCallback', 'membership.google.callback');
$router->post('/{lang}/membership/auth/google/callback', 'MembershipController@googleCallback', 'membership.google.callback.post');

$router->get('/{lang}/membership/logout', 'MembershipController@logout', 'membership.logout');
$router->post('/{lang}/membership/logout', 'MembershipController@logout', 'membership.logout.post');
$router->get('/{lang}/membership/dashboard', 'MembershipController@dashboard', 'membership.dashboard');
$router->get('/{lang}/membership/verify', 'MembershipController@verifyCard', 'membership.verify');
$router->post('/{lang}/membership/payment', 'MembershipController@makePayment', 'membership.payment');
$router->post('/{lang}/membership/transition', 'MembershipController@requestTransition', 'membership.transition');
$router->post('/{lang}/membership/profile/update', 'MembershipController@updateProfile', 'membership.profile.update');
$router->post('/{lang}/membership/password/update', 'MembershipController@updatePassword', 'membership.password.update');
$router->get('/{lang}/membership/card/print', 'MembershipController@printCard', 'membership.card.print');

// Admin Membership Administration (Super Admin, Admin, Membership Officer)
$router->get('/{lang}/admin/members', 'AdminController@members', 'admin.members');
$router->post('/{lang}/admin/members/approve/{id}', 'AdminController@approveMember', 'admin.members.approve');
$router->post('/{lang}/admin/members/reject/{id}', 'AdminController@rejectMember', 'admin.members.reject');
$router->post('/{lang}/admin/members/suspend/{id}', 'AdminController@suspendMember', 'admin.members.suspend');
$router->post('/{lang}/admin/members/activate/{id}', 'AdminController@activateMember', 'admin.members.activate');
$router->post('/{lang}/admin/members/transition/{id}', 'AdminController@transitionMember', 'admin.members.transition');
$router->post('/{lang}/admin/members/payment/verify/{id}', 'AdminController@verifyMemberPayment', 'admin.members.payment.verify');
$router->post('/{lang}/admin/members/payment/reject/{id}', 'AdminController@rejectMemberPayment', 'admin.members.payment.reject');

// SPS Official Invoice & Money Receipt Routes (Members & Non-Members)
$router->get('/{lang}/invoice', 'InvoiceController@lookup', 'invoice.lookup');
$router->get('/{lang}/invoice/{id}', 'InvoiceController@show', 'invoice.show');
$router->post('/{lang}/donation/submit', 'InvoiceController@submitDonation', 'donation.submit');




