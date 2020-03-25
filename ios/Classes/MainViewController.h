/*

File: MainViewController.h
Abstract: Controller class for the "main" view (visible at app start).


*/

#import "MyCLController.h"
#import "RootViewController.h"
#import <WebKit/WebKit.h>

@interface MainViewController : UIViewController <MyCLControllerDelegate,WKNavigationDelegate> {
	IBOutlet UITextView *updateTextView;
    //IBOutlet UIWebView *webView;
	
    IBOutlet WKWebView *webView;
    
    
    IBOutlet UIBarButtonItem *startStopButton;
	IBOutlet UIBarButtonItem *mailButton;
	IBOutlet UIActivityIndicatorView *spinner;
	BOOL isCurrentlyUpdating;
	BOOL isCurrentlySending;
	BOOL firstUpdate;
	id flipDelegate;
    //NSURLRequest *theRequest;
}

@property (nonatomic,assign) id <MyFlipControllerDelegate> flipDelegate;
@property (nonatomic, retain) UITextView *updateTextView;
//@property (nonatomic, retain) UIWebView *webView;
@property (nonatomic, retain) WKWebView *webView;

@property (nonatomic, retain) UIBarButtonItem *startStopButton;
@property (nonatomic, retain) UIBarButtonItem *mailButton;
@property (nonatomic, retain) UIActivityIndicatorView *spinner;
//@property (nonatomic, retain) NSURLRequest *theRequest;

- (IBAction)infoButtonPressed:(id)sender;
- (IBAction)startStopButtonPressed:(id)sender;
- (IBAction)mailButtonPressed:(id)sender;

@end
