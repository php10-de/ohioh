/*

File: FlipsideViewController.h
Abstract: Controller class for the "flipside" view, which contains all the
controls for settings.

*/

#import "MyCLController.h"
#import "RootViewController.h"

@interface FlipsideViewController : UIViewController <UITextFieldDelegate> {
	IBOutlet UISwitch *distanceFilterSwitch;
	IBOutlet UISlider *distanceFilterSlider;
	IBOutlet UILabel *distanceFilterValueLabel;
	IBOutlet UILabel *distanceFilterSliderLabel1;
	IBOutlet UILabel *distanceFilterSliderLabel2;
	IBOutlet UILabel *distanceFilterSliderLabel3;
	IBOutlet UILabel *distanceFilterSliderLabel4;
	IBOutlet UITextField *urlTextField;
	NSArray *pickerItems;
	NSArray *filterControls;
	
	CLLocationDistance previousFilter, filterToSet;
    NSString *previousUrl;
    NSString *urlToSet;

	id flipDelegate;
}

@property (nonatomic,assign) id <MyFlipControllerDelegate> flipDelegate;
@property (nonatomic, retain) UISwitch *distanceFilterSwitch;
@property (nonatomic, retain) UISlider *distanceFilterSlider;
@property (nonatomic, retain) UITextField *urlTextField;
@property (nonatomic, retain) UILabel *distanceFilterValueLabel;
@property (nonatomic, retain) UILabel *distanceFilterSliderLabel1;
@property (nonatomic, retain) UILabel *distanceFilterSliderLabel2;
@property (nonatomic, retain) UILabel *distanceFilterSliderLabel3;
@property (nonatomic, retain) UILabel *distanceFilterSliderLabel4;
@property (nonatomic, retain) NSArray *filterControls;

- (IBAction)doneButtonPressed:(id)sender;
- (IBAction)switchAction:(id)sender;
- (IBAction)sliderValueChanged:(id)sender;
- (void)setControlStatesFromSource:(MyCLController *)clDelegate;

@end
