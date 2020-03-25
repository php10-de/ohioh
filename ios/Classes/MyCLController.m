/*

File: MyCLController.m
Abstract: Singleton class used to talk to CoreLocation and send website url back to
the app's view controllers.


*/

#import "MyCLController.h"

// Shorthand for getting localized strings, used in formats below for readability
#define LocStr(key) [[NSBundle mainBundle] localizedStringForKey:(key) value:@"" table:nil]


// This is a singleton class, see below
static MyCLController *sharedCLDelegate = nil;

@implementation MyCLController

@synthesize delegate, locationManager;

- (id) init {
	self = [super init];
	if (self != nil) {
		self.locationManager = [[[CLLocationManager alloc] init] autorelease];
		self.locationManager.delegate = self; // Tells the location manager to send updates to this object
	}
	return self;
}


// Called when the location is updated
- (void)locationManager:(CLLocationManager *)manager
	didUpdateToLocation:(CLLocation *)newLocation
		   fromLocation:(CLLocation *)oldLocation
{
	NSMutableString *update = [[[NSMutableString alloc] init] autorelease];
	
	// Timestamp
	NSDateFormatter *dateFormatter = [[[NSDateFormatter alloc] init]  autorelease];
	//[dateFormatter setDateStyle:NSDateFormatterMediumStyle];
	[dateFormatter setTimeStyle:NSDateFormatterMediumStyle];
	//[update appendFormat:@"Last Update: %@\n", [dateFormatter stringFromDate:newLocation.timestamp]];
	
	// Horizontal coordinates
	if (signbit(newLocation.horizontalAccuracy)) {
		// Negative accuracy means an invalid or unavailable measurement
		[update appendString:LocStr(@"LatLongUnavailable")];
	} else {
		// CoreLocation returns positive for North & East, negative for South & West
		/*
         [update appendFormat:LocStr(@"LatLongFormat"), // This format takes 4 args: 2 pairs of the form coordinate + compass direction
		 fabs(newLocation.coordinate.latitude), signbit(newLocation.coordinate.latitude) ? LocStr(@"South") : LocStr(@"North"),
		 fabs(newLocation.coordinate.longitude),	signbit(newLocation.coordinate.longitude) ? LocStr(@"West") : LocStr(@"East")];
		[update appendString:@"\n"];
         */
		[update appendFormat:LocStr(@"MeterAccuracyFormat"), newLocation.horizontalAccuracy];
        NSDateFormatter *urlFormatter = [[[NSDateFormatter alloc] init] autorelease];
        [urlFormatter setDateStyle: NSDateFormatterNoStyle];
        
        NSString *urlSuff = [[NSString alloc] init];
        //NSString *userUrl = [[NSString alloc] init];
        NSString *urlChar = [[NSString alloc] init];
        //userUrl = [[NSUserDefaults standardUserDefaults] objectForKey:@"url"];
        if ([[[NSUserDefaults standardUserDefaults] objectForKey:@"url"] rangeOfString: @"?"].length) {
            urlChar = @"&";
        } else {
            urlChar = @"?";
        }
        if ([[[NSUserDefaults standardUserDefaults] objectForKey:@"url"] rangeOfString: @"http"].length) {
            urlSuff = @"";
        } else {
            urlSuff = @"http://";
        }
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        [defaults setFloat:newLocation.coordinate.latitude forKey:@"latitude"];
        [defaults setFloat:newLocation.coordinate.longitude forKey:@"longitude"];
        [defaults setFloat:newLocation.altitude forKey:@"altitude"];
        [defaults setFloat:newLocation.horizontalAccuracy forKey:@"horizontalAccuracy"];
        [defaults setFloat:newLocation.verticalAccuracy forKey:@"verticalAccuracy"];
        [defaults setInteger:newLocation.timestamp forKey:@"timestamp"];
        
        UIDevice *device = [UIDevice currentDevice];

        NSString  *currentDeviceId = [[device identifierForVendor]UUIDString];
        
        
        
        NSString *url = [NSString stringWithFormat: @"%@%@%@iphone_id=%@&lat=%.6f&lon=%.6f&alt=%.6f&acc=%.0f&t=%.0f&timezone=%d&version=1.5",urlSuff,[[NSUserDefaults standardUserDefaults] objectForKey:@"url"],urlChar,currentDeviceId,newLocation.coordinate.latitude,newLocation.coordinate.longitude,newLocation.altitude,newLocation.horizontalAccuracy,[newLocation.timestamp timeIntervalSince1970], [[NSTimeZone defaultTimeZone] secondsFromGMT]];
        //NSString *url = [NSString stringWithFormat: @"%@%@%@iphone_id=%s&lat=35.578592&lon=103.215179&alt=%.6f&acc=%.0f&t=%.0f&timezone=%d",urlSuff,[[NSUserDefaults standardUserDefaults] objectForKey:@"url"],urlChar,[[[UIDevice currentDevice] uniqueIdentifier] UTF8String],newLocation.altitude,newLocation.horizontalAccuracy,[newLocation.timestamp timeIntervalSince1970], [[NSTimeZone defaultTimeZone] secondsFromGMT]];
        //Paris 48.857939,2.362404, Berlin 52.533767,13.41156, Avignon 43.951305,4.809952, Tokio 35.669325,139.825916, Washingto D.C. 38.898515,-77.023773, China 35.578592,103.215179
        [self.delegate phpUpdate:url];
        [urlSuff release];
        [urlChar release];
        //[userUrl release];*/
        //NSLog(url);
	}
	//[update appendString:@"\n\n"];
	
    /*
	// Altitude
	if (signbit(newLocation.verticalAccuracy)) {
		// Negative accuracy means an invalid or unavailable measurement
		[update appendString:LocStr(@"AltUnavailable")];
	} else {
		// Positive and negative in altitude denote above & below sea level, respectively
		[update appendFormat:LocStr(@"AltitudeFormat"), fabs(newLocation.altitude),	(signbit(newLocation.altitude)) ? LocStr(@"BelowSeaLevel") : LocStr(@"AboveSeaLevel")];
		[update appendString:@"\n"];
		[update appendFormat:LocStr(@"MeterAccuracyFormat"), newLocation.verticalAccuracy];
	}
	[update appendString:@"\n\n"];
     */
	
	// Calculate disatance moved and time elapsed, but only if we have an "old" location
	//
	// NOTE: Timestamps are based on when queries start, not when they return. CoreLocation will query your
	// location based on several methods. Sometimes, queries can come back in a different order from which
	// they were placed, which means the timestamp on the "old" location can sometimes be newer than on the
	// "new" location. For the example, we will clamp the timeElapsed to zero to avoid showing negative times
	// in the UI.
	//
    /*
	if (oldLocation != nil) {
		CLLocationDistance distanceMoved = [newLocation getDistanceFrom:oldLocation];
		NSTimeInterval timeElapsed = [newLocation.timestamp timeIntervalSinceDate:oldLocation.timestamp];
		
		[update appendFormat:LocStr(@"LocationChangedFormat"), distanceMoved];
		if (signbit(timeElapsed)) {
			[update appendString:LocStr(@"FromPreviousMeasurement")];
		} else {
			[update appendFormat:LocStr(@"TimeElapsedFormat"), timeElapsed];
		}
		[update appendString:@"\n\n"];
	}
     */
	
	// Send the update to our delegate
	[self.delegate newLocationUpdate:update];
}

- (void)startSignificantChangeUpdates
{
    // Create the location manager if this object does not
    // already have one.
    if (nil == locationManager)
        locationManager = [[CLLocationManager alloc] init];
    
    locationManager.delegate = self;
    [locationManager startMonitoringSignificantLocationChanges];
    NSLog(@"Test Update Location");
}

// Called when there is an error getting the location
- (void)locationManager:(CLLocationManager *)manager
	   didFailWithError:(NSError *)error
{
	NSMutableString *errorString = [[[NSMutableString alloc] init] autorelease];

	if ([error domain] == kCLErrorDomain) {

		// We handle CoreLocation-related errors here

		switch ([error code]) {
			// This error code is usually returned whenever user taps "Don't Allow" in response to
			// being told your app wants to access the current location. Once this happens, you cannot
			// attempt to get the location again until the app has quit and relaunched.
			//
			// "Don't Allow" on two successive app launches is the same as saying "never allow". The user
			// can reset this for all apps by going to Settings > General > Reset > Reset Location Warnings.
			//
			case kCLErrorDenied:
				[errorString appendFormat:@"%@\n", NSLocalizedString(@"LocationDenied", nil)];
				break;

			// This error code is usually returned whenever the device has no data or WiFi connectivity,
			// or when the location cannot be determined for some other reason.
			//
			// CoreLocation will keep trying, so you can keep waiting, or prompt the user.
			//
			case kCLErrorLocationUnknown:
				[errorString appendFormat:@"%@\n", NSLocalizedString(@"LocationUnknown", nil)];
				break;

			// We shouldn't ever get an unknown error code, but just in case...
			//
			default:
				[errorString appendFormat:@"%@ %d\n", NSLocalizedString(@"GenericLocationError", nil), [error code]];
				break;
		}
	} else {
		// We handle all non-CoreLocation errors here
		// (we depend on localizedDescription for localization)
		[errorString appendFormat:@"Error domain: \"%@\"  Error code: %d\n", [error domain], [error code]];
		[errorString appendFormat:@"Description: \"%@\"\n", [error localizedDescription]];
	}

	// Send the update to our delegate
	[self.delegate newLocationUpdate:errorString];

}

#pragma mark ---- singleton object methods ----

// See "Creating a Singleton Instance" in the Cocoa Fundamentals Guide for more info

+ (MyCLController *)sharedInstance {
    @synchronized(self) {
        if (sharedCLDelegate == nil) {
            [[self alloc] init]; // assignment not done here
        }
    }
    return sharedCLDelegate;
}

+ (id)allocWithZone:(NSZone *)zone {
    @synchronized(self) {
        if (sharedCLDelegate == nil) {
            sharedCLDelegate = [super allocWithZone:zone];
            return sharedCLDelegate;  // assignment and return on first allocation
        }
    }
    return nil; // on subsequent allocation attempts return nil
}

- (id)copyWithZone:(NSZone *)zone
{
    return self;
}

- (id)retain {
    return self;
}

- (unsigned)retainCount {
    return UINT_MAX;  // denotes an object that cannot be released
}

- (void)release {
    //do nothing
}

- (id)autorelease {
    return self;
}

@end
