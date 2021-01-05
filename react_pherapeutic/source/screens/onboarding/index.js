import Swiper from 'react-native-swiper';
import {
  View,
  Text,
  Image,
  TouchableOpacity,
  Dimensions
} from 'react-native';
import styles from './styles';
import { connect } from 'react-redux';
import constants from '../../utils/constants';
import React, { useState } from 'react';
import LinearGradient from 'react-native-linear-gradient';
import SubmitButton from '../../components/submitButton';

const { height, width } = Dimensions.get('window');

const OnboardingScreens = (props) => {
  const [index, setIndex] = useState(0);

  const { navigation } = props;

  const Dot = () => {
    return (
      <View style={{
        backgroundColor: constants.colors.activeDot,
        width: 14,
        height: 14,
        borderRadius: 7,
        margin: 2,
        marginBottom: height * 0.04
      }} />
    )
  }

  const ActiveDot = () => {
    return (
      <View style={{
        backgroundColor: constants.colors.dot,
        width: 14,
        height: 14,
        borderRadius: 7,
        margin: 2,
        marginBottom: height * 0.04
      }} />
    )
  }

  const TopView = (props) => {
    const { last } = props;
    return (
      <View style={styles.backButtonView} >
        <TouchableOpacity
          onPress={() => navigation.navigate('app', { screen: 'Home' })}
          style={{ justifyContent: 'center', alignItems: 'center' }}
        >
          <LinearGradient
            start={{ x: 0, y: 1 }}
            end={{ x: 1, y: 1 }}
            colors={["#228994", "#3BD8E5"]}
            style={{
              justifyContent: 'center',
              alignItems: 'center',
              height: Dimensions.get('window').height * 0.032,
              width: last ? Dimensions.get('window').width * 0.20 : Dimensions.get('window').width * 0.15,
              borderRadius: 3,
            }}
          >
            <Text
              style={{
                color: constants.colors.white,
                fontWeight: '500',
                fontSize: 13
              }}
            >{last ? 'Get Started' : 'Skip'}</Text>
          </LinearGradient>
        </TouchableOpacity>
      </View>
    )
  }

  return (
    <Swiper
      containerStyle={styles.container}
      loop={false}
      index={0}
      autoplay={false}
      dot={<Dot />}
      activeDot={<ActiveDot />}
      showsButtons={false}
      scrollEnabled={true}
      horizontal={true}
    >
      <View style={styles.container} >
        <Image
          source={constants.images.onboard_1}
          resizeMode={'stretch'}
          style={styles.containerBackground}
        />
        <View style={{ flex: 1, justifyContent: 'flex-start', alignItems: 'center' }} >
          <TopView />
        </View>
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }} >
          <View style={styles.headingWrap} >
            <Text style={styles.headingText} >How it works</Text>
          </View>
          <Text style={styles.contentText} >Pherapeutic makes its possible for you</Text>
          <Text style={styles.contentText} >to access the right talking therapies at</Text>
          <Text style={styles.contentText} >the time you need it the most - your</Text>
          <Text style={styles.contentText} >point of crisis</Text>
        </View>
      </View>

      <View style={styles.container} >
        <Image
          source={constants.images.onboard_2}
          resizeMode={'stretch'}
          style={styles.containerBackground}
        />
        <View style={{ flex: 1, justifyContent: 'flex-start', alignItems: 'center' }} >
          <TopView />
        </View>
        <View style={{ flex: 1.5, justifyContent: 'center', alignItems: 'center' }} >
          <View style={styles.headingWrap} >
            <Text style={styles.headingText} >We know it can be difficult</Text>
            <Text style={styles.headingText} >to take that first step</Text>
          </View>

          <Text style={styles.contentText} >So we’ve made it easier for you.</Text>
          <Text style={styles.contentText} > Whether you’re suffering from</Text>
          <Text style={styles.contentText} > Depression, Anxiety, PTSD, Eating</Text>
          <Text style={styles.contentText} >Disorders or other mental health issue,</Text>
          <Text style={styles.contentText} >we have a range of therapists available</Text>
          <Text style={styles.contentText} >at the touch of a button</Text>
        </View>
      </View>

      <View style={styles.container} >
        <Image
          source={constants.images.onboard_3}
          resizeMode={'stretch'}
          style={styles.containerBackground}
        />
        <View style={{ flex: 1, justifyContent: 'flex-start', alignItems: 'center' }} >
          <TopView />
        </View>
        <View style={{ flex: 1.5, justifyContent: 'center', alignItems: 'center' }} >
          <View style={styles.headingWrap} >
            <Text style={styles.headingText} >You don’t have to suffer in</Text>
            <Text style={styles.headingText} >silence anymore</Text>
          </View>

          <Text style={styles.contentText} >To get started, simply sign up to the</Text>
          <Text style={styles.contentText} >app and complete the my profile</Text>
          <Text style={styles.contentText} >section. We’ve worked with a range of</Text>
          <Text style={styles.contentText} >psychologists and psychotherapists to</Text>
          <Text style={styles.contentText} >develop a questionnaire that helps to</Text>
          <Text style={styles.contentText} >provide a diagnosis of your mental</Text>
          <Text style={styles.contentText} >health issue</Text>
        </View>
      </View>

      <View style={styles.container} >
        <Image
          source={constants.images.onboard_4}
          resizeMode={'stretch'}
          style={styles.containerBackground}
        />
        <View style={{ flex: 1, justifyContent: 'flex-start', alignItems: 'center' }} >
          <TopView />
        </View>
        <View style={{ flex: 1.5, justifyContent: 'center', alignItems: 'center' }} >
          <View style={styles.headingWrap} >
            <Text style={styles.headingText} >With pherapeutic, help is</Text>
            <Text style={styles.headingText} > always on hand right</Text>
            <Text style={styles.headingText} >when you need it</Text>
          </View>

          <Text style={styles.contentText} >We will match you to the right therapist</Text>
          <Text style={styles.contentText} > who is available to help talk you</Text>
          <Text style={styles.contentText} > through your crisis</Text>
        </View>
      </View>

      <View style={styles.container} >
        <Image
          source={constants.images.onboard_5}
          resizeMode={'stretch'}
          style={styles.containerBackground}
        />
        {/* <View style={{ flex: 1, justifyContent: 'flex-start', alignItems: 'center' }} >
          <TopView last={true} />
        </View> */}
        <View style={{ flex: 1 }} />
        <View style={{ flex: 1.5, justifyContent: 'center', alignItems: 'center' }} >
          <View style={styles.headingWrap} >
            <Text style={styles.headingText} >Let's get started</Text>
          </View>

          <Text style={styles.contentText} >Your life only gets better when you do.</Text>
          <Text style={styles.contentText} >Work on yourself and rest will follow.</Text>
          <View style={{ marginVertical: height * 0.04 }} >

            <SubmitButton
              title={`Let's Go`}
              submitFunction={() => navigation.navigate('app', { screen: 'Home' })}
            />
          </View>
        </View>
      </View>
    </Swiper>
  )
};

const mapStateToProps = (state) => ({

})

export default connect(mapStateToProps)(OnboardingScreens);