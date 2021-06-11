import React, {useState, useEffect, useRef} from 'react';
import {
  View,
  Text,
  Image,
  TouchableOpacity,
  Dimensions,
  ScrollView,
} from 'react-native';
import {connect} from 'react-redux';
import styles from './styles';
import QuestionItem from './questionItem';
import AwesomeAlert from 'react-native-awesome-alerts';
import constants from '../../utils/constants';
import Modal from 'react-native-modal';
import CustomButton from '../../components/CustomButton';
import Events from '../../utils/events';
import APICaller from '../../utils/APICaller';
import SubmitButton from '../../components/submitButton';
import LinearGradient from 'react-native-linear-gradient';
const {height, width} = Dimensions.get('window');

const QuestionBot = (props) => {
  const [showAlert, setShowAlert] = useState(false);
  const [message, setMessage] = useState('Thank You');
  const [questionsList, setQuestions] = useState([]);
  const [noOfAnswers, setNoOfAnswers] = useState(0);
  const [currentquestion, setquestioncurrent] = useState();
  const [isModalVisible, setModalVisible] = useState(false);
  const scrollRef = useRef();

  const {navigation, dispatch, userData, userToken} = props;

  useEffect(() => {
    getQuestions();
  }, []);

  const getQuestions = () => {
    const endpoint = 'user/questions';
    const method = 'GET';
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    APICaller(endpoint, method, null, headers)
      .then((response) => {
        console.log('response getting questions => ', response['data']);
        const {status, statusCode, message, data} = response['data'];
        if (status === 'success') {
          setQuestions([...data]);
        }
      })
      .catch((error) => {
        console.log('error getting questions => ', error['data']);
      });
  };

  const selectAnswer = (answerData) => {
    const endpoint = 'user/answers';
    const method = 'POST';
    const body = {
      user_id: userData.user_id,
      question_id: answerData.question_id,
      answer_id: answerData.id,
    };
    const headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userToken}`,
      Accept: 'application/json',
    };
    console.log('body to send => ', body);
    APICaller(endpoint, method, body, headers)
      .then((response) => {
        console.log('response after answering question => ', response['data']);
        const {status, statusCode, message, data} = response['data'];
        if (status === 'success') {
          setquestioncurrent(answerData.question_id);
          if (currentquestion != answerData.question_id) {
            setNoOfAnswers((prevNo) => prevNo + 1);
          }
        }
      })
      .catch((error) => {
        console.log('error after answering question => ', error['data']);
      });
  };

  return (
    <View style={styles.outerView}>
      <Image
        source={constants.images.background}
        resizeMode={'stretch'}
        style={styles.containerBackground}
      />
      <ScrollView
        ref={scrollRef}
        onContentSizeChange={() => {
          scrollRef.current.scrollToEnd();
        }}
        showsVerticalScrollIndicator={false}
        contentContainerStyle={styles.container}>
        <View style={styles.backButtonView}>
          <TouchableOpacity
            onPress={() => navigation.navigate('MyProfile')}
            style={styles.backButtonWrap}>
            <Image
              source={constants.images.backArrowWhite}
              style={{height: 18, width: 10, margin: 10}}
            />
          </TouchableOpacity>
          <View
            style={{flex: 4, justifyContent: 'center', alignItems: 'center'}}>
            <Text style={[styles.headingText, {textAlign: 'center'}]}>
              To get started, we just{'\n'}need to learn a bit{'\n'}more about
              you
            </Text>
          </View>
          <View style={{flex: 1}}>
            <TouchableOpacity
              // onPress={() => navigation.navigate('TherapistDetails')}
              onPress={() => setModalVisible(true)}
              style={{justifyContent: 'center', alignItems: 'center'}}>
              <LinearGradient
                start={{x: 0, y: 1}}
                end={{x: 1, y: 1}}
                colors={['#228994', '#3BD8E5']}
                style={{
                  justifyContent: 'center',
                  alignItems: 'center',
                  height: Dimensions.get('window').height * 0.032,
                  width: Dimensions.get('window').width * 0.15,
                  borderRadius: 3,
                }}>
                <Text
                  style={{
                    color: constants.colors.white,
                    fontWeight: '500',
                    fontSize: 13,
                  }}>
                  {'Skip'}
                </Text>
              </LinearGradient>
            </TouchableOpacity>
          </View>
        </View>
        <View style={{width: width, paddingHorizontal: width * 0.05}}>
          <Text style={{color: constants.colors.white, fontSize: 14}}>
            Please select one answer.
          </Text>
        </View>

        {questionsList.length ? (
          questionsList.map((question, index) => {
            if (index <= noOfAnswers)
              return (
                <QuestionItem selectAnswer={selectAnswer} data={question} />
              );
          })
        ) : (
          <View />
        )}
        {questionsList.length === noOfAnswers ? (
          <View
            style={{
              justifyContent: 'center',
              alignItems: 'center',
              marginBottom: height * 0.05,
              marginTop: height * 0.08,
            }}>
            <SubmitButton
              title={'CONTINUE'}
              submitFunction={() => navigation.navigate('TherapistDetails')}
            />
          </View>
        ) : (
          <View />
        )}

        {isModalVisible == true ? (
          <View
            style={{
              //flex: 1,
              // height: Dimensions.get('window').height,
              // width: Dimensions.get('window').width,
              backgroundColor: 'white',
            }}>
            <Modal animationType="fade" transparent={true} isVisible={true}>
              <View
                style={{
                  alignContent: 'center',
                  justifyContent: 'center',
                  backgroundColor: 'white',
                  /// marginHorizontal: 10,
                  borderRadius: 10,

                  marginTop: 10,
                  padding: 10,

                  // height:
                  //   Platform.OS == 'android'
                  //     ? Dimensions.get('window').height / 1.8
                  //     : Dimensions.get('window').height / 2.3,
                  // width: Dimensions.get('window').width,
                }}>
                <Image
                  style={{alignSelf: 'center'}}
                  source={constants.images.Internerconnection}
                />
                <Text
                  style={{
                    textAlign: 'center',
                    fontSize: 18,
                    padding: 25,

                    color: constants.colors.fieldName,
                  }}>
                  To speak to a therapist, you will be prompted to enter your
                  payment details on the call screen. But don’t worry, you
                  should only have to do this once.
                </Text>
                <TouchableOpacity
                  style={{
                    alignItems: 'center',
                    justifyContent: 'center',
                    // flex: 0.5,
                    // marginHorizontal: 20,
                    backgroundColor: '#228994',
                    padding: 10,
                    borderRadius: 5,
                    marginTop: 10,
                  }}
                  onPress={() => {
                    navigation.navigate('TherapistDetails');
                    setModalVisible(false);
                  }}>
                  <Text
                    style={{
                      fontWeight: '500',
                      fontSize: 18,
                      color: constants.colors.white,
                    }}>
                    Ok
                  </Text>
                </TouchableOpacity>
              </View>
            </Modal>
          </View>
        ) : null}
      </ScrollView>

      <AwesomeAlert
        show={showAlert}
        showProgress={false}
        message={message}
        closeOnTouchOutside={true}
        showConfirmButton={true}
        confirmText="Confirm"
        confirmButtonColor={constants.colors.lightGreen}
        onCancelPressed={() => {
          setShowAlert(false);
        }}
        onConfirmPressed={() => {
          setShowAlert(false);
        }}
        onDismiss={() => {
          setShowAlert(false);
        }}
      />
    </View>
  );
};

const mapStateToProps = (state) => ({
  userData: state.user.userData,
  userToken: state.user.userToken,
});

export default connect(mapStateToProps)(QuestionBot);
