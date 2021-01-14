import { createStackNavigator } from '@react-navigation/stack';
import React, { useState, useEffect } from 'react';
import Modal from 'react-native-modal';
import { NavigationContainer } from '@react-navigation/native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Text, View, Dimensions, Alert, Image, TouchableOpacity } from 'react-native';
import NetInfo from "@react-native-community/netinfo";
import { SkypeIndicator } from 'react-native-indicators';
import constants from '../utils/constants';
import { connect } from 'react-redux';
import { saveUser } from '../redux/actions/user';
import Events from '../utils/events';
import LoginScreen from '../screens/login';
import SignUpScreen from '../screens/signUp';
import MyProfileScreen from '../screens/myProfile';
import MapScreen from '../screens/mapScreen';
import MapDetailScreen from '../screens/mapDetailScreen';
import ViewProfileScreen from '../screens/viewProfile';
import EditProfileScreen from '../screens/editProfile';
import VerifyOTPScreen from '../screens/verifyEmail';
import OnboardingScreens from '../screens/onboarding';
import AuthOtionsScreen from '../screens/authOptions';
import GetStartedScreen from '../screens/getStarted';
import ChangePasswordScreen from '../screens/changePassword';
import TherapistStatusScreen from '../screens/therapistStatus';
import QuestionBotScreen from '../screens/questionBot';
import HomeScreen from '../screens/home';
import ForgotPasswordScreen from '../screens/forgotPassword';
import ResetPasswordScreen from '../screens/resetPassword';
import TherapistDetailsScreen from '../screens/therapistDetails';
import AboutUs from '../screens/aboutUs';
import ContactUs from '../screens/contactUs';
import PrivacyPolicy from '../screens/privacyPolicy';
import TermsAndConditions from '../screens/termsAndConditions';

import { utils } from '@react-native-firebase/app';
import messaging, { firebase } from '@react-native-firebase/messaging';
import VideoCallScreen from '../screens/videoCall';
import addCard from '../screens/payment';
import ManagePayment from '../screens/managePaymentMethod';
import RatingList from '../screens/ratingList';
import Review from '../screens/review';
import LeaveNote from '../screens/leaveNote';
import selectPaymentMethod from '../screens/selectPaymentMethod';
import faq from '../screens/faq';
import AppointmentHistory from '../screens/appointmentHistory';
import PaymentHistory from '../screens/paymentHistory';
import AppointmentHistoryModel from '../screens/appointmentHistoryModel';

const Stack = createStackNavigator();
const RootNavigator = (props) => {
  const [user, setUser] = useState(null);
  const [userStatus, setUserStatus] = useState(null);
  const [isConnected, setConnected] = useState('');
  const [isModalVisible, setModalVisible] = useState(false);
  //const [initialRoute, setInitialRoute] = useState('TherapistDetails')
  const [initialRoute, setInitialRoute] = useState('Home');

  Events.on('logoutUser', 'lu#1', () => {
    console.log('loging out!!!');
    setUserStatus(false);
  });

  const AuthStack = () => {
    return (
      <Stack.Navigator screenOptions={{ headerShown: false }}>
        <Stack.Screen name="AuthOptions" component={AuthOtionsScreen} />
        <Stack.Screen
          name="ResetPassword"
          header={null}
          component={ResetPasswordScreen}
        />
        <Stack.Screen name="Login" header={null} component={LoginScreen} />
        <Stack.Screen name="GetStarted" component={GetStartedScreen} />
        <Stack.Screen name="Onboarding" component={OnboardingScreens} />
        <Stack.Screen name="SignUp" header={null} component={SignUpScreen} />
        <Stack.Screen
          name="ForgotPassword"
          header={null}
          component={ForgotPasswordScreen}
        />
        <Stack.Screen
          name="VerifyEmail"
          header={null}
          component={VerifyOTPScreen}
        />
        <Stack.Screen
          name="TermsAndConditions"
          component={TermsAndConditions}
        />
      </Stack.Navigator>
    );
  };

  const AppStack = () => {
    return (
      <Stack.Navigator
        initialRouteName={initialRoute}
        screenOptions={{ headerShown: false }}>
        <Stack.Screen name="MyProfile" component={MyProfileScreen} />
        <Stack.Screen name="VideoCall" component={VideoCallScreen} />
        <Stack.Screen name="Home" component={HomeScreen} />
        <Stack.Screen name="QuestionBot" component={QuestionBotScreen} />
        <Stack.Screen
          name="TherapistStatus"
          component={TherapistStatusScreen}
        />
        <Stack.Screen name="ViewProfile" component={ViewProfileScreen} />
        <Stack.Screen name="EditProfile" component={EditProfileScreen} />
        <Stack.Screen name="ChangePassword" component={ChangePasswordScreen} />
        <Stack.Screen name="AboutUs" component={AboutUs} />
        <Stack.Screen name="ContactUs" component={ContactUs} />
        <Stack.Screen name="PrivacyPolicy" component={PrivacyPolicy} />

        <Stack.Screen name="AddCard" component={addCard} />
        <Stack.Screen name="ManagePayment" component={ManagePayment} />
        <Stack.Screen
          name="selectPaymentMethod"
          component={selectPaymentMethod}
        />
        <Stack.Screen
          name="TermsAndConditions"
          component={TermsAndConditions}
        />
        <Stack.Screen name="MapScreen" component={MapScreen} />
        <Stack.Screen name="MapDetailScreen" component={MapDetailScreen} />
        <Stack.Screen
          name="TherapistDetails"
          component={TherapistDetailsScreen}
        />
        <Stack.Screen name="RatingList" component={RatingList} />
        <Stack.Screen name="LeaveNote" component={LeaveNote} />
        <Stack.Screen name="Review" component={Review} />
        <Stack.Screen name="faq" component={faq} />
        <Stack.Screen name="AppointmentHistory" component={AppointmentHistory} />
        <Stack.Screen name="PaymentHistory" component={PaymentHistory} />
        <Stack.Screen name="AppointmentHistoryModel" component={AppointmentHistoryModel} />

      </Stack.Navigator>
    );
  };
  useEffect(() => {
    const { dispatch } = props;
    
    AsyncStorage.getItem('userData').then((user) => {
      if (user) {
        const userData = JSON.parse(user);
        //   console.log('user data is => ', userData);
        dispatch(saveUser(userData));
        setUserStatus(true);
      } else {
        setUserStatus(false);
      }
    });
    

    //  console.log('Welcome To Pherapeutic !!!', userStatus);
  }, [userStatus]);
  //  useEffect(() => {
  // const unsubscribe = NetInfo.addEventListener(state => {
  //   console.log("Connection type", state.type);
  //   console.log("Is connected?1", state.isConnected);
  //   if (state.isConnected == true) {
  //     setModalVisible(false)
  //     setConnected(state.isConnected)
  //   }
  //   else {
  //     setConnected(state.isConnected)
  //     setModalVisible(true)
  //   }

  // });
  //   }, [])

  // useEffect(() => {
  //   const unsubscribe = messaging().onMessage(async remoteMessage => {
  //     console.log(JSON.stringify(remoteMessage))
  //     // Alert.alert('A new FCM message arrived!', JSON.stringify(remoteMessage));
  // //  setInitialRoute('VideoCall')

  //    Alert.alert(
  //     '',
  //    ''+remoteMessage.notification.title+'',
  //    [
  //      {
  //        text: "Cancel",
  //        onPress: () => console.log("Cancel Pressed"),
  //        style: "cancel"
  //      },
  //      { text: "Join",
  //     onPress: () =>setInitialRoute('VideoCall')
  //     }
  //    ],
  //    { cancelable: false }
  //  );
  //   });
  //   return unsubscribe;
  // }, [initialRoute])

  // if (isConnected == false) {
  //   return (
  //     <View
  //       style={{
  //         flex: 1,
  //         justifyContent: 'center',
  //         alignItems: 'center',
  //         height: Dimensions.get('window').height,
  //         width: Dimensions.get('window').width,
  //         backgroundColor: 'transparent',
  //       }}>
  //       <Modal isVisible={isModalVisible}>
  //         <View style={{ backgroundColor: 'white', borderRadius: 5, padding: 10 }}>
  //           <Image style={{ alignSelf: 'center' }} source={constants.images.Internerconnection} />
       


  //         </View>
  //       </Modal>
  //     </View>
  //   );

  // }


   if (userStatus === null) {
    return (
      <View
        style={{
          flex: 1,
          justifyContent: 'center',
          alignItems: 'center',
          height: Dimensions.get('window').height,
          width: Dimensions.get('window').width,
          backgroundColor: 'transparent',
        }}>
        <SkypeIndicator color={constants.colors.pink} />
      </View>
    );
  } else if (userStatus === false) {
    return (
      <NavigationContainer>
        <Stack.Navigator screenOptions={{ headerShown: false }}>
          <Stack.Screen name="auth" component={AuthStack} />
          <Stack.Screen name="app" component={AppStack} />
        </Stack.Navigator>
      </NavigationContainer>
    );
  } else if (userStatus === true) {
    return (
      <NavigationContainer>
        <Stack.Navigator screenOptions={{ headerShown: false }}>
          <Stack.Screen name="app" component={AppStack} />
          <Stack.Screen name="auth" component={AuthStack} />
        </Stack.Navigator>
      </NavigationContainer>
    );
  }
};

export default connect()(RootNavigator);
