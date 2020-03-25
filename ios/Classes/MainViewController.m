/*
 
 File: MainViewController.m
 Abstract: Controller class for the "main" view (visible at app start).
 
 */

#import "MainViewController.h"
#import <WebKit/WebKit.h>



@implementation MainViewController

@synthesize flipDelegate, updateTextView, webView, startStopButton, mailButton, spinner;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    if (self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil]) {
        isCurrentlyUpdating = NO;
        isCurrentlySending = NO;
        firstUpdate = YES;
    }
    return self;
}


- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation {
    // Return YES for supported orientations
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}


- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning]; // Releases the view if it doesn't have a superview
    // Release anything that's not essential, such as cached data
}


- (void)dealloc {
    [updateTextView release];
    webView.navigationDelegate = nil;
    [webView release];
    [startStopButton release];
    [spinner release];
    
    [webView release];
    [super dealloc];
}


// Appends some text to the main text view
// If this is the first update, it will replace the existing text
-(void)addTextToLog:(NSString *)text {
    if (firstUpdate) {
        updateTextView.text = text;
        firstUpdate = NO;
    } else {
        updateTextView.text = text;//[NSString stringWithFormat:@"%@-----\n\n%@", updateTextView.text, text];
        //[updateTextView scrollRangeToVisible:NSMakeRange([updateTextView.text length], 0)]; // scroll to the bottom on updates
    }
}

// Called when the view is loading for the first time only
// If you want to do something every time the view appears, use viewWillAppear or viewDidAppear
- (void)viewDidLoad {
    [MyCLController sharedInstance].delegate = self;
    webView.navigationDelegate = self;
    
    // Check to see if the user has disabled location services all together
    // In that case, we just print a message and disable the "Start" button
    if (![CLLocationManager locationServicesEnabled]) {
        [self addTextToLog:NSLocalizedString(@"NoLocationServices", @"User disabled location services")];
        startStopButton.enabled = NO;
    } else {
        [self startStopButtonPressed: self.flipDelegate];
    }
    

    
    
}

#pragma mark ---- control callbacks ----

// Tells the root view, via a delegate, to flip over to the FlipSideView
- (IBAction)infoButtonPressed:(id)sender {
    [self.flipDelegate toggleView:self];
}

// Tells the root view, via a delegate, to flip over to the FlipSideView
- (IBAction)mailButtonPressed:(id)sender {
    //NSLog(@"Mail senden");
    
    NSString *sUrl = [NSString stringWithFormat: @"mailto:?subject=location&body=http://maps.google.com/maps?q=%.6f,%.6f",[[NSUserDefaults standardUserDefaults] floatForKey:@"latitude"],[[NSUserDefaults standardUserDefaults] floatForKey:@"longitude"]];
    NSURL *url = [[NSURL alloc] initWithString:sUrl];
    [[UIApplication sharedApplication] openURL:url];
}

// Called when the "start/stop" button is pressed
-(IBAction)startStopButtonPressed:(id)sender {
    if (isCurrentlyUpdating) {
        [[MyCLController sharedInstance].locationManager stopUpdatingLocation];
        isCurrentlyUpdating = NO;
        isCurrentlySending = NO;
        [startStopButton setTitle:NSLocalizedString(@"StartButton", @"Start")];
        //[webView stopLoading];
        [spinner stopAnimating];
    } else {
        [[MyCLController sharedInstance].locationManager startUpdatingLocation];
        isCurrentlyUpdating = YES;
        [startStopButton setTitle:NSLocalizedString(@"StopButton", @"Stop")];
        [spinner startAnimating];
    }
}

#pragma mark ---- delegate methods for the MyCLController class ----

-(void)newLocationUpdate:(NSString *)text {
    [self addTextToLog:text];
}

-(void)phpUpdate:(NSString *)url {
    // create the request
    if (isCurrentlySending == NO) {
        isCurrentlySending = YES;
        NSHTTPURLResponse   * response;
        NSError             * error;
        //[[MyCLController sharedInstance].locationManager stopUpdatingLocation]; // warten, bis Seite geladen ist
        NSURLRequest *theRequest=[NSURLRequest requestWithURL:[NSURL URLWithString:url]
                                                  cachePolicy:NSURLRequestReturnCacheDataElseLoad
                                              timeoutInterval:30.0];
        [spinner startAnimating];
        
        [NSURLConnection sendSynchronousRequest:theRequest returningResponse:&response error:&error];
        //NSLog(@"RESPONSE HEADERS: \n%@", [response allHeaderFields]);
        
        // check if this is OK (not a 404 or the like)
        if([response respondsToSelector:@selector(statusCode)]){
            int statusCode = [(NSHTTPURLResponse *)response statusCode];
            if(statusCode >= 400){
                isCurrentlySending = NO;
                [startStopButton setTitle:NSLocalizedString(@"StartButton", @"Start")];
                [spinner stopAnimating];
            }
        }
        
        [webView loadRequest:theRequest];
    }
}

//- (void)webViewDidFinishLoad:(UIWebView *)webView
//{
//    isCurrentlySending = NO;
//    [spinner stopAnimating];
//	if (isCurrentlyUpdating) { // weitermachen!
//        //[[MyCLController sharedInstance].locationManager startUpdatingLocation];
//    }
//}


- (void)webView:(WKWebView *)webView didFinishNavigation:(WKNavigation *)navigation{
    
    isCurrentlySending = NO;
    [spinner stopAnimating];
    if (isCurrentlyUpdating) { // weitermachen!
        //[[MyCLController sharedInstance].locationManager startUpdatingLocation];
    }
    
    
    
}


- (void)webView:(WKWebView *)webView didFailNavigation:(WKNavigation *)navigation withError:(NSError *)error{
    isCurrentlySending = NO;
    [startStopButton setTitle:NSLocalizedString(@"StartButton", @"Start")];
    [spinner stopAnimating];
    if (isCurrentlyUpdating) { // weitermachen!
        //[[MyCLController sharedInstance].locationManager startUpdatingLocation];
    }
    
    // inform the user
    //[self addTextToLog:[NSString stringWithFormat: @"Load failed! Error - %@",
    //                    [error localizedDescription]]];
    [self addTextToLog:NSLocalizedString(@"LoadFailed",@"Load failed!")];
    
    
    
    
}





//- (void)webView:(UIWebView *)webView didFailLoadWithError:(NSError *)error {
//
//}



-(void)newError:(NSString *)text {
    [self addTextToLog:text];
}

@end
