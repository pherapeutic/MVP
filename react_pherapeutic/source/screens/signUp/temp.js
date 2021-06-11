import React, { useState, } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  Dimensions,
  Image,
  TextInput,
  ScrollView
} from 'react-native';
import constants from '../../utils/constants';
import styles from './styles';
import SubmitButton from '../../components/submitButton';
import APICaller from '../../utils/APICaller';

const { height, width } = Dimensions.get('window');

const SignUp = (props) => {
  const [first_name, setFirstName] = useState('');
  const [last_name, setLastName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirm_password, setConfirmPassword] = useState('');
  const [role, setRole] = useState('Client');
  const [language, setLanguage] = useState('English');
  const [language_id, setLanguage_id] = useState('1');

  const signUpHandler = () => {
    console.log("first_name!! => ",first_name);
    console.log("last_name!! => ",last_name);
    console.log("email!! => ",email);
    console.log("password!! => ",password);
    console.log("confirm_password!! => ",confirm_password);
    console.log("role!! => ",role);
    console.log("language_id!! => ",language_id);
    
    if (first_name &&
      last_name &&
      email &&
      password &&
      confirm_password &&
      role &&
      language_id &&
      password === confirm_password
    ) {
      const registerObj = {
        first_name,
        last_name,
        email,
        password,
        confirm_password,
        role,
        language_id
      }
      console.log('register object => ', registerObj)
      APICaller('register', 'POST', registerObj)
        .then(response => {
         // console.warn('response after register => ', response);
        })
        .catch(error => {
        //  console.warn('error after register => ', error);
        })
    }
  }

  return (
    <ScrollView
      showsVerticalScrollIndicator={false}
      contentContainerStyle={{ width: '100%', flex: 1 }}
    >
      <View style={styles.container} >
        <Image source={constants.images.background} style={styles.containerBackground} />
        <Image source={constants.images.formsBackground} resizeMode={'stretch'} style={styles.formsBackground} />
        <View style={styles.backButtonView} >
          <View style={{ flex: 1, justifyContent: 'center', alignItems: 'flex-end' }} >
            <Image source={constants.images.backIcon} style={{ height: 18, width: 10 }} />
          </View>
          <View style={{ flex: 8, justifyContent: 'center', alignItems: 'center' }} >
            <Image
              source={constants.images.logo}
              resizeMode={'contain'}
              style={{
                height: Dimensions.get('window').height * 0.09,
                width: Dimensions.get('window').width * 0.75
              }} />
          </View>
          <View style={{ flex: 1 }} />
        </View>
        <View style={styles.formView} >

          <View style={styles.formWrap} >

            <View style={styles.formField} >
              <Text style={styles.fieldName} >FIRST NAME</Text>
              <View style={styles.fieldInputWrap} >
                <TextInput
                  style={styles.fieldInput}
                  onChangeText={text => setFirstName(text)}
                  value={first_name}
                />
              </View>
            </View>

            <View style={styles.formField} >
              <Text style={styles.fieldName} >LAST NAME</Text>
              <View style={styles.fieldInputWrap} >
                <TextInput
                  style={styles.fieldInput}
                  onChangeText={text => setLastName(text)}
                  value={last_name}
                />
              </View>
            </View>

            <View style={styles.formField} >
              <Text style={styles.fieldName} >EMAIL ADDRESS</Text>
              <View style={styles.fieldInputWrap} >
                <TextInput
                  style={styles.fieldInput}
                  onChangeText={text => setEmail(text)}
                  value={email}
                />
              </View>
            </View>

            <View style={styles.formField} >
              <Text style={styles.fieldName} >CREATE PASSWORD</Text>
              <View style={styles.fieldInputWrap} >
                <TextInput
                  style={styles.fieldInput}
                  secureTextEntry={true}
                  onChangeText={text => setPassword(text)}
                  value={password}
                />
              </View>
            </View>

            <View style={styles.formField} >
              <Text style={styles.fieldName} >CONFIRM PASSWORD</Text>
              <View style={styles.fieldInputWrap} >
                <TextInput
                  style={styles.fieldInput}
                  secureTextEntry={true}
                  onChangeText={text => setConfirmPassword(text)}
                  value={confirm_password}
                />
              </View>
            </View>

            <View style={styles.formField} >
              <Text style={styles.fieldName} >ARE YOU A THERAPIST OR A CLIENT?</Text>
              <View style={styles.fieldInputWrap} >
                <TextInput
                  style={styles.fieldInput}
                  onChangeText={text => setRole(text)}
                  value={role}
                  editable={false}
                />
              </View>
            </View>

            <View style={styles.formField} >
              <Text style={styles.fieldName} >LANGUAGE YOU SPEAK</Text>
              <View style={styles.fieldInputWrap} >
                <TextInput
                  style={styles.fieldInput}
                  onChangeText={text => setLanguage(text)}
                  value={language}
                  editable={false}
                />
              </View>
            </View>

            <SubmitButton
              title={'SIGN UP'}
              colors={['rgb(191, 53, 160)', 'rgb(62, 218, 243)']}
              submitFunction={() => signUpHandler()}
            />
          </View>
        </View>
        <View style={styles.footerView} >
          <Text style={styles.footerText} >Already signed up? <Text style={styles.footerlinkText} >Login</Text></Text>
        </View>
      </View>
    </ScrollView>
  )
};

export default SignUp;