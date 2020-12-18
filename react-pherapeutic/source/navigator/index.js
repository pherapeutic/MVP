import { createStackNavigator } from '@react-navigation/stack';
import React, { useState, useEffect } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Text, View, Dimensions, Alert } from 'react-native';
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
import { utils } from '@react-native-firebase/app';
import messaging, { firebase } from '@react-native-firebase/messaging';
import VideoCallScreen from '../screens/videoCall';

const Stack = createStackNavigator();


const RootNavigator = (props) => {
  const [user, setUser] = useState(null);
  const [userStatus, setUserStatus] = useState(null);
  const [initialRoute, setInitialRoute] = useState('VideoCall')

  Events.on('logoutUser', 'lu#1', () => {
    console.log('loging out!!!');
    setUserStatus(false);
  });

  const AuthStack = () => {
    return (
      <Stack.Navigator screenOptions={{ headerShown: false }}>
        <Stack.Screen name="AuthOptions" component={AuthOtionsScreen} />
        <Stack.Screen name="ResetPassword" header={null} component={ResetPasswordScreen} />
        <Stack.Screen name="Login" header={null} component={LoginScreen} />
        <Stack.Screen name="GetStarted" component={GetStartedScreen} />
        <Stack.Screen name="Onboarding" component={OnboardingScreens} />
        <Stack.Screen name="SignUp" header={null} component={SignUpScreen} />
        <Stack.Screen name="ForgotPassword" header={null} component={ForgotPasswordScreen} />
        <Stack.Screen name="VerifyEmail" header={null} component={VerifyOTPScreen} />
      </Stack.Navigator>
    );
  };

  const AppStack = () => {
    return (
      <Stack.Navigator initialRouteName={initialRoute} screenOptions={{ headerShown: false }} >
        <Stack.Screen name="MyProfile" component={MyProfileScreen} />
        <Stack.Screen name="VideoCall" component={VideoCallScreen} />
        <Stack.Screen name="Home" component={HomeScreen} />
        <Stack.Screen name="QuestionBot" component={QuestionBotScreen} />
        <Stack.Screen name="TherapistStatus" component={TherapistStatusScreen} />
        <Stack.Screen name="ViewProfile" component={ViewProfileScreen} />
        <Stack.Screen name="EditProfile" component={EditProfileScreen} />
        <Stack.Screen name="ChangePassword" component={ChangePasswordScreen} />
        <Stack.Screen name="MapScreen" component={MapScreen} />
        <Stack.Screen name="MapDetailScreen" component={MapDetailScreen} />
        <Stack.Screen name="TherapistDetails" component={TherapistDetailsScreen} />
      </Stack.Navigator>
    );
  };

  useEffect(() => {
    const { dispatch } = props;
    AsyncStorage.getItem('userData').then((user) => {
      if (user) {
        const userData = JSON.parse(user);
        console.log('user data is => ', userData);
        dispatch(saveUser(userData));
        setUserStatus(true);
      } else {
        setUserStatus(false);
      }
    });
    console.log('Welcome To Pherapeutic !!!', userStatus);


  }, [userStatus]);

  useEffect(() => {
    const unsubscribe = messaging().onMessage(async remoteMessage => {
      // Alert.alert('A new FCM message arrived!', JSON.stringify(remoteMessage));
      setInitialRoute('VideoCall')
    });
    return unsubscribe;
  }, [initialRoute])





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
