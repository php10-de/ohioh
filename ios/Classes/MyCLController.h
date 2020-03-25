/*

File: MyCLController.h
Abstract: Singleton class used to talk to CoreLocation and send results back to
the app's view controllers.

*/


// This protocol is used to send the text for location updates back to another view controller
@protocol MyCLControllerDelegate <NSObject>
@required
-(void)newLocationUpdate:(NSString *)text;
-(void)phpUpdate:(NSString *)url;
-(void)newError:(NSString *)text;
@end


// Class definition
@interface MyCLController : NSObject <CLLocationManagerDelegate> {
	CLLocationManager *locationManager;
	id delegate;
}

@property (nonatomic, retain) CLLocationManager *locationManager;
@property (nonatomic,assign) id <MyCLControllerDelegate> delegate;


- (void)locationManager:(CLLocationManager *)manager
	didUpdateToLocation:(CLLocation *)newLocation
		   fromLocation:(CLLocation *)oldLocation;

- (void)locationManager:(CLLocationManager *)manager
	   didFailWithError:(NSError *)error;

+ (MyCLController *)sharedInstance;

@end

