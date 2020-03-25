/*

File: FlipsideViewController.m
Abstract: Controller class for the "flipside" view, which contains all the
controls for settings.

*/

#import "FlipsideViewController.h"

@implementation FlipsideViewController

@synthesize flipDelegate, filterControls;
@synthesize distanceFilterSwitch, distanceFilterSlider, distanceFilterValueLabel;
@synthesize distanceFilterSliderLabel1, distanceFilterSliderLabel2, distanceFilterSliderLabel3, distanceFilterSliderLabel4;
@synthesize urlTextField;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
	if (self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil]) {
	}
	return self;
}

- (void)viewDidLoad {
	urlTextField.delegate = self;
	// Special color for "reverse" views
	self.view.backgroundColor = [UIColor viewFlipsideBackgroundColor];
	
	// This array contains all the controls toggled by the "Distance Filter" switch
	// This allows us to easily enable / disable the controls when the state of the switch changes
	// We do this here instead of in the init because the objects are nil while in the init method
	self.filterControls = [NSArray arrayWithObjects:distanceFilterSlider, distanceFilterValueLabel, distanceFilterSliderLabel1, distanceFilterSliderLabel2, distanceFilterSliderLabel3, distanceFilterSliderLabel4, nil];
}

- (void)viewWillAppear:(BOOL)animated {
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    previousUrl = urlToSet = [defaults objectForKey:@"url"];
	self.urlTextField.text = previousUrl;
	//[distanceFilterSlider setValue:[defaults floatForKey:@"filter"]];
	// Set up the controls to the right initial values, based on the current state of the CoreLocation object
	[self setControlStatesFromSource:[MyCLController sharedInstance]];
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation {
	return (interfaceOrientation == UIInterfaceOrientationPortrait); // Portrait mode only
}


- (void)didReceiveMemoryWarning {
	[super didReceiveMemoryWarning]; // Releases the view if it doesn't have a superview
	// Release anything that's not essential, such as cached data
}

- (void)dealloc {
	[distanceFilterSwitch release];
	[distanceFilterSlider release];
	[urlTextField release];
	[distanceFilterValueLabel release];
	[distanceFilterSliderLabel1 release];
	[distanceFilterSliderLabel2 release];
	[distanceFilterSliderLabel3 release];
	[distanceFilterSliderLabel4 release];
    
	[filterControls release];
	[super dealloc];
}

#pragma mark ---- UIPickerViewDataSource delegate methods ----

// returns the number of columns to display.
- (NSInteger)numberOfComponentsInPickerView:(UIPickerView *)pickerView {
	return 1;
}

// returns the number of rows
- (NSInteger)pickerView:(UIPickerView *)pickerView numberOfRowsInComponent:(NSInteger)component {
	return [pickerItems count];
}

#pragma mark ---- Other control callbacks ----

// Causes the new settings to take effect
// Tells the root view via a delegate to flip back over to the main view
- (IBAction)doneButtonPressed:(id)sender {
	// This is where we actually set the values that the user has chosen. 
	if (filterToSet != previousFilter) {
		[MyCLController sharedInstance].locationManager.distanceFilter = filterToSet;
        
        //[defaults setFloat:self.distanceFilterSlider.value forKey:@"filter"];
        //NSLog(@"%.1f",[defaults floatForKey:@"filter"]);
	}
    urlToSet = self.urlTextField.text;
    if (![urlToSet rangeOfString:@"://"].length && ![urlToSet rangeOfString:@"www."].length && [urlToSet hasSuffix:@".map"]) {
    //urlToSet = [self.urlTextField.text stringByReplacingOccurrencesOfString:@"map.php" withString:@"geo.php"];
        urlToSet = [NSString stringWithFormat:@"https://maps.location-sender.com/%@/geo.php",[urlToSet stringByReplacingOccurrencesOfString:@".map" withString:[NSString stringWithFormat:@""]]];
    }
    urlToSet = [urlToSet stringByReplacingOccurrencesOfString:@"www.php10.de/maps" withString:[NSString stringWithFormat:@"maps.location-sender.com"]];
    if (urlToSet != previousUrl) {
		
		if ([urlToSet length] == 0) {
			//text = @"Please enter the URL";
		} else {
            NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
			[defaults setObject:urlToSet forKey:@"url"];
		}
    }

	// Tell the root view to flip back over to the main view
	[self.flipDelegate toggleView:self];
}

-(BOOL)textFieldShouldReturn:(UITextField *)urlTextField {
	[self.urlTextField resignFirstResponder];
	return YES;
}

// Toggles the "enabled" state all of the controls associated with the filter value
- (void) setFilterControlsEnabled:(BOOL)state {
	for (UIControl *control in filterControls) {
		control.enabled = state;
	}
}

// Called when the switch is flipped
- (IBAction)switchAction:(id)sender {
	BOOL newState = [sender isOn];
	
	// Controls are disabled if switch is off
	[self setFilterControlsEnabled:newState];
    
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    [defaults setBool:newState forKey:@"distanceswitch"];
	
	// If the switch is on, distance filter is current slider value, otherwise set it to "none"
	if ( newState ) {
		[self sliderValueChanged:self];
	} else {
		filterToSet = kCLDistanceFilterNone;
	}
}

	// Takes the value of the slider and puts it in a label underneath it
- (void) updateSliderLabel {
	double value = pow(10, [distanceFilterSlider value]);
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    [defaults setFloat:[distanceFilterSlider value] forKey:@"distancefilter"];
	distanceFilterValueLabel.text = [NSString stringWithFormat:@"%.0f %@", value, (value < 1.5) ? NSLocalizedString(@"MeterSingular", @"meter") : NSLocalizedString(@"MeterPlural", @"meters")];
}

// Called when the slider is moved, with live updating
- (IBAction)sliderValueChanged:(id)sender {
	// The distance filter slider is an exponential scale, base 10.
	// Slider returns a value in the range 0.0 to 3.0 (set in Interface Builder).
	// Corresponds to a range of 1 to 1000, exponentially.
	filterToSet = pow(10, distanceFilterSlider.value);
	[self updateSliderLabel];
}

// Sets the state of the controls based on the state of the location manager
- (void) setControlStatesFromSource:(MyCLController *) clDelegate {
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    distanceFilterSlider.value = [defaults floatForKey:@"distancefilter"];
	[distanceFilterSwitch setOn:[defaults boolForKey:@"distanceswitch"] animated:NO];
    
	// Current values from the source object
	previousFilter = filterToSet = clDelegate.locationManager.distanceFilter;
	
	// Set up filter controls
	// Since the slider is exponential (base 10), converting the other way is done with a base 10 logarithm.
	// If the filter is set to "none", we choose a default value of 10 (log10 of which is 1, hard coded below).
	//[distanceFilterSwitch setOn:(previousFilter != kCLDistanceFilterNone) animated:NO];
	if ([defaults floatForKey:@"distancefilter"] > 0) {
        [distanceFilterSlider setValue: [defaults floatForKey:@"distancefilter"] animated:NO];
    } else {
        [distanceFilterSlider setValue:((previousFilter == kCLDistanceFilterNone) ? 1 : log10(fabs(previousFilter))) animated:NO];
	}
    [self updateSliderLabel];
	[self setFilterControlsEnabled:[distanceFilterSwitch isOn]];
}	

@end
