var FiltersEnabled = 0; // if your not going to use transitions or filters in any of the tips set this to 0
var spacer="&nbsp; &nbsp; &nbsp; ";

// email notifications to admin
notifyAdminNewMembers0Tip=["", spacer+"No email notifications to admin."];
notifyAdminNewMembers1Tip=["", spacer+"Notify admin only when a new member is waiting for approval."];
notifyAdminNewMembers2Tip=["", spacer+"Notify admin for all new sign-ups."];

// visitorSignup
visitorSignup0Tip=["", spacer+"If this option is selected, visitors will not be able to join this group unless the admin manually moves them to this group from the admin area."];
visitorSignup1Tip=["", spacer+"If this option is selected, visitors can join this group but will not be able to sign in unless the admin approves them from the admin area."];
visitorSignup2Tip=["", spacer+"If this option is selected, visitors can join this group and will be able to sign in instantly with no need for admin approval."];

// titles table
titles_addTip=["",spacer+"This option allows all members of the group to add records to the 'Website Details' table. A member who adds a record to the table becomes the 'owner' of that record."];

titles_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Website Details' table."];
titles_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Website Details' table."];
titles_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Website Details' table."];
titles_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Website Details' table."];

titles_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Website Details' table."];
titles_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Website Details' table."];
titles_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Website Details' table."];
titles_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Website Details' table, regardless of their owner."];

titles_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Website Details' table."];
titles_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Website Details' table."];
titles_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Website Details' table."];
titles_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Website Details' table."];

// links table
links_addTip=["",spacer+"This option allows all members of the group to add records to the 'Links' table. A member who adds a record to the table becomes the 'owner' of that record."];

links_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Links' table."];
links_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Links' table."];
links_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Links' table."];
links_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Links' table."];

links_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Links' table."];
links_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Links' table."];
links_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Links' table."];
links_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Links' table, regardless of their owner."];

links_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Links' table."];
links_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Links' table."];
links_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Links' table."];
links_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Links' table."];

// blog_categories table
blog_categories_addTip=["",spacer+"This option allows all members of the group to add records to the 'Blog categories' table. A member who adds a record to the table becomes the 'owner' of that record."];

blog_categories_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Blog categories' table."];
blog_categories_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Blog categories' table."];
blog_categories_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Blog categories' table."];
blog_categories_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Blog categories' table."];

blog_categories_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Blog categories' table."];
blog_categories_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Blog categories' table."];
blog_categories_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Blog categories' table."];
blog_categories_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Blog categories' table, regardless of their owner."];

blog_categories_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Blog categories' table."];
blog_categories_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Blog categories' table."];
blog_categories_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Blog categories' table."];
blog_categories_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Blog categories' table."];

// blogs table
blogs_addTip=["",spacer+"This option allows all members of the group to add records to the 'Blogs' table. A member who adds a record to the table becomes the 'owner' of that record."];

blogs_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Blogs' table."];
blogs_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Blogs' table."];
blogs_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Blogs' table."];
blogs_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Blogs' table."];

blogs_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Blogs' table."];
blogs_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Blogs' table."];
blogs_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Blogs' table."];
blogs_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Blogs' table, regardless of their owner."];

blogs_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Blogs' table."];
blogs_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Blogs' table."];
blogs_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Blogs' table."];
blogs_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Blogs' table."];

// editors_choice table
editors_choice_addTip=["",spacer+"This option allows all members of the group to add records to the 'Editors choice' table. A member who adds a record to the table becomes the 'owner' of that record."];

editors_choice_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Editors choice' table."];
editors_choice_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Editors choice' table."];
editors_choice_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Editors choice' table."];
editors_choice_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Editors choice' table."];

editors_choice_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Editors choice' table."];
editors_choice_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Editors choice' table."];
editors_choice_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Editors choice' table."];
editors_choice_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Editors choice' table, regardless of their owner."];

editors_choice_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Editors choice' table."];
editors_choice_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Editors choice' table."];
editors_choice_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Editors choice' table."];
editors_choice_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Editors choice' table."];

/*
	Style syntax:
	-------------
	[TitleColor,TextColor,TitleBgColor,TextBgColor,TitleBgImag,TextBgImag,TitleTextAlign,
	TextTextAlign,TitleFontFace,TextFontFace, TipPosition, StickyStyle, TitleFontSize,
	TextFontSize, Width, Height, BorderSize, PadTextArea, CoordinateX , CoordinateY,
	TransitionNumber, TransitionDuration, TransparencyLevel ,ShadowType, ShadowColor]

*/

toolTipStyle=["white","#00008B","#000099","#E6E6FA","","images/helpBg.gif","","","","\"Trebuchet MS\", sans-serif","","","","3",400,"",1,2,10,10,51,1,0,"",""];

applyCssFilter();
